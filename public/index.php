<?php

use App\Entity\User;
use App\Handler\Entity\EntityManager;
use App\Routing\Router;
use Dotenv\Dotenv;

require "../vendor/autoload.php";

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

$router = new Router(
    require_once('../src/Routing/routes.php')
);
new EntityManager();

// If there is a match, he will return the class and method associated
// to the request as well as route parameters
if ($match = $router->match()) {
    $controller = new $match['class']();
    $controller->{$match['method']}($match['params']);
}
