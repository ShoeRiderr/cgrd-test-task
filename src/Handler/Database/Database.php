<?php

namespace App\Handler\Database;

use Exception;
use Exception\Database\NoQueryException;
use PDO;
use PDOException;

final class Database
{
    protected static ?array $instances = [];

    protected static PDO $connection;

    protected $query;

    protected function __construct()
    {
        $this->setConnection();
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
        if (!isset(self::$instances[self::class])) self::$instances[self::class] = new self();

        return self::$connection;
    }

    public static function getInstance()
    {
        $subclass = static::class;

        if (!isset(self::$instances[$subclass])) {
            // Note that here we use the "static" keyword instead of the actual
            // class name. In this context, the "static" keyword means "the name
            // of the current class". That detail is important because when the
            // method is called on the subclass, we want an instance of that
            // subclass to be created here.

            self::$instances[$subclass] = new static();
        }

        return self::$instances[$subclass];
    }

    protected function setConnection(): void
    {
        $dbHost = $_ENV['DB_HOST'];
        $dbDatabase = $_ENV['DB_DATABASE'];
        $dbUser = $_ENV['DB_USER'];
        $dbPassword = $_ENV['DB_PASSWORD'];

        self::$connection = new PDO(
            sprintf(
                'mysql:host=%s; dbname=%s',
                $dbHost,
                $dbDatabase
            ),
            $dbUser,
            $dbPassword
        );
    }

    public function query(string $query): self
    {
        $this->query = $this->connection->query($query);

        return $this;
    }

    /**
     * @return array|bool
     */
    public function getAll()
    {
        if (!$this->query) {
            throw new NoQueryException('getAll');
        }

        return $this->query->fetchAll();
    }
}
