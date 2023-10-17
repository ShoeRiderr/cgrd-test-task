<?php

declare(strict_types=1);

namespace App;

use App\Exception\Routing\RouteNotFoundException;
use App\Handler\Database\Database;
use App\Handler\Entity\EntityManager;
use App\Handler\Routing\Router;
use Throwable;
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

        session_start();

        static::$twig->addGlobal('session', $_SESSION);
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
            echo $this->router->resolve();
        } catch (RouteNotFoundException $e) {
            http_response_code(404);

            echo static::$twig->render('fail_response/not_found.html.twig');
        } catch (Throwable $e) {
            error_log($e->getMessage());

            http_response_code(500);

            echo static::$twig->render('fail_response/server_error.html.twig');
        }
    }
}
