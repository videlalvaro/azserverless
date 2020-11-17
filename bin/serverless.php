<?php
declare(strict_types=1);

if (file_exists(__DIR__ . '/vendor/autoload.php')) { // the script is being run on the project's main folder
    require_once __DIR__ . '/vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/../../../autoload.php')) { // the script is being run out of the vendor/videlalvaro/azserverless/bin folder
    /** @noinspection PhpIncludeInspection */
    require_once __DIR__ . '/../../../autoload.php';
} else {
    /** @noinspection PhpIncludeInspection */
    require_once __DIR__ . '/../vendor/autoload.php'; // the script is being run in the project's bin folder
}

use Azserverless\Runtime\Router;
$router = new Router(getcwd());
$response = $router->route();

header("Content-type: application/json");
echo(json_encode($response));
