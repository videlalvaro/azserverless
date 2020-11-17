<?php

namespace Azserverless\Runtime;

use Azserverless\Context\FunctionContext;
use Monolog\Logger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\TestHandler;

class Router {
    private $context;
    private $log;
    private $logHandler;
    private $baseFunctionsDir;

    public function __construct($baseFunctionsDir) {
        $this->baseFunctionsDir = $baseFunctionsDir;
        // TODO maybe create a more specific log handler. 
        // So far TestHandler works for our purposes.
        $this->logHandler = new TestHandler();
        $this->log = new Logger('serverless');
        $this->log->pushHandler($this->logHandler);
        $this->context = new FunctionContext($this->log);

        set_exception_handler(array($this, 'exceptionHandler'));
    }

    protected function exceptionHandler($exception) {
        $this->context->log->error($exception);
        $response = [
            'Outputs' => NULL,
            'ReturnValue' => NULL,
            'Logs' => $this->getLogs()
        ];
        header("Content-type: application/json");
        echo(json_encode($response));
        exit(1);
    }

    public function route() {
        $requestUri = $_SERVER['REQUEST_URI'];
        $requestBody = file_get_contents('php://input');
        $request = json_decode($requestBody, true);

        $this->context->inputs = $request['Data'];

        // ob_start();

        // TODO check if file exists, if it doesn't throw exception, else load.
        $handler = $this->baseFunctionsDir . $requestUri . '/index.php';
        if (file_exists($handler)) {
            require_once($handler);
        } else {
            throw new Exception(sprintf("Cannot find handler: %s", $requestUri));
        }

        $returnValue = run($this->context);

        // $functionOut = ob_get_contents();

        // $this->log->info($functionOut);

        // ob_end_clean();

        if (is_null($returnValue)) {
            $returnValue = "";
        }

        return [
            'Outputs' => $this->context->outputs,
            'ReturnValue' => $returnValue,
            'Logs' => $this->getLogs()
        ];
    }

    private function getLogs() {
        $logs = [];
        $formatter = new LineFormatter("[%datetime%] %channel%.%level_name%: %message%");
        foreach($this->logHandler->getRecords() as $record) {
            $logs[] = $formatter->format($record);
        }
        return $logs;
    }
}