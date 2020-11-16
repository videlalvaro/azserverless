<?php

namespace Azserverless\Context;

class FunctionContext {
    public $inputs = [];
    public $outputs;
    public $log;

    public function __construct($log) {
        $this->log = $log;
        $this->outputs = [ '_none_' => null ];
    }
}