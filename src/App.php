<?php

declare(strict_types = 1);

namespace App;

use App\Exception\RouteNotFoundException;
use App\Handler\Container;
use App\Handler\Database\Database;
use App\Handler\Entity\EntityManager;
use App\Handler\Routing\Router;

class App
{
    private static Database $db;
    private static EntityManager $entityManager;

    public function __construct(protected Router $router, protected Config $config)
    {
        static::$db = new Database($config->db ?? []);
        static::$entityManager = new EntityManager();
    }

    public static function db(): Database
    {
        return static::$db;
    }

    public static function entityManager(): EntityManager
    {
        return static::$entityManager;
    }

    public function run()
    {
        try {
            echo $this->router->resolve($this->router);
        } catch (RouteNotFoundException) {
            http_response_code(404);
        }
    }
}