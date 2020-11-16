<?php
declare(strict_types=1);

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/../autoload.php')) {
    /** @noinspection PhpIncludeInspection */
    require_once __DIR__ . '/../autoload.php';
} else {
    /** @noinspection PhpIncludeInspection */
    require_once __DIR__ . '/../vendor/autoload.php';
}

use Azserverless\Runtime\Router;
$router = new Router(getcwd());
$response = $router->route();

header("Content-type: application/json");
echo(json_encode($response));
