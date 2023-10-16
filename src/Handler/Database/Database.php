<?php

namespace App\Handler\Database;

use App\Handler\Container;
use Exception;
use Exception\Database\NoQueryException;
use PDO;
use PDOException;

final class Database
{
    protected static $instance = '';

    protected static PDO $connection;

    protected $query;

    private PDO $pdo;

    public function __construct(
        array $config,
        private Container $container
    ) {
        $this->setConnection($config);
    }

    protected function __clone()
    {
    }

    public function __wakeup()
    {
        throw new Exception("Cannot unserialize Database class");
    }

    public function __destruct()
    {
        $this->query = null;
    }

    public static function getConnection()
    {
        return self::$connection;
    }

    public function getInstance()
    {
        if (!isset(self::$instance)) {
            // Note that here we use the "static" keyword instead of the actual
            // class name. In this context, the "static" keyword means "the name
            // of the current class". That detail is important because when the
            // method is called on the subclass, we want an instance of that
            // subclass to be created here.

            self::$instance = $this->container->get(self::class);
        }

        return self::$instance;
    }

    protected function setConnection(array $config): void
    {
        $defaultOptions = [
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        try {
            $this->pdo = new PDO(
                $config['driver'] . ':host=' . $config['host'] . ';dbname=' . $config['database'],
                $config['user'],
                $config['pass'],
                $config['options'] ?? $defaultOptions
            );
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int) $e->getCode());
        }
    }
}
