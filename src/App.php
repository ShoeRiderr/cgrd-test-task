<?php

declare(strict_types=1);

namespace App;

use App\Exception\RouteNotFoundException;
use App\Handler\Database\Database;
use App\Handler\Entity\EntityManager;
use App\Handler\Routing\Router;
use Twig\Environment as TwigEnvironment;
use Twig\Loader\FilesystemLoader;

class App
{
    private static Database $db;
    private static EntityManager $entityManager;
    private static TwigEnvironment $twig;

    public function __construct(protected Router $router, protected Config $config)
    {
        static::$db = new Database($config->db ?? []);
        static::$entityManager = new EntityManager();

        $loader = new FilesystemLoader('../templates');

        static::$twig = new TwigEnvironment($loader);
    }

    public static function db(): Database
    {
        return static::$db;
    }

    public static function entityManager(): EntityManager
    {
        return static::$entityManager;
    }

    public static function twig(): TwigEnvironment
    {
        return static::$twig;
    }

    public function run()
    {
        try {
            echo $this->router->resolve($this->router);
        } catch (RouteNotFoundException $e) {
            // var_dump($e);
            http_response_code(404);
        }
    }
}
