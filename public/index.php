<?php

use App\Controllers\DashboardController;
use App\Routing\Router;

require "../vendor/autoload.php";

$router = new Router(
    require_once('../src/Routing/routes.php')
);

// If there is a match, he will return the class and method associated
// to the request as well as route parameters
if ($match = $router->match()) {
    $controller = new $match['class']();
    $controller->{$match['method']}($match['params']);
}
