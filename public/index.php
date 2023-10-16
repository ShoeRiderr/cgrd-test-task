<?php

use App\App;
use App\Config;
use App\Handler\Container;
use App\Handler\Routing\Router;
use Dotenv\Dotenv;

require "../vendor/autoload.php";

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

$container = new Container();

$router = new Router(
    $container,
    require_once('../routes/web.php')
);

(new App(
    $router,
    new Config($_ENV)
))->run();