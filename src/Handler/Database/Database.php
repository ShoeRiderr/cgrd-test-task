<?php

declare(strict_types=1);

namespace App\Handler\Database;

use PDO;

/**
 * @mixin PDO
 */
class Database
{
    private PDO $pdo;

    public function __construct(array $config)
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
            error_log($e->getMessage());

            throw new \PDOException($e->getMessage(), (int) $e->getCode());
        }
    }

    /**
     * Every attempt of call on class method will try to execute PDO instance method
     */
    public function __call(string $name, array $arguments)
    {
        return call_user_func_array([$this->pdo, $name], $arguments);
    }
}
