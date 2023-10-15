<?php

namespace App\Handler\Repository;

use App\DbConnection\Database;
use App\Handler\Entity\EntityManager;

abstract class Repository
{
    protected $conn;
    protected string $table = '';

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function findById(string|int $id): array
    {
        return $this->conn->prepare('SELECT * FROM :table WHERE id = :id')
            ->bindValue('id', $id)
            ->execute();
    }

    public function fetchAll()
    {
        $entityRepository = EntityManager::getRepositoryData(self::class);

        return $this->conn
            ->query('SELECT * FROM ' . $entityRepository['table'])
            ->fetchAll();
    }
}
