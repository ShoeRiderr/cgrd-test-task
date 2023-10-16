<?php

namespace App\Handler\Repository;

use App\Handler\Database\Database;
use App\Handler\Entity\EntityManager;
use Exception;
use PDO;

abstract class Repository
{
    private $instance = '';
    protected $conn = '';
    protected string $entity = '';
    protected string $table = '';
    protected string $entityData = '';
    protected string $entityRepository = '';

    public function __construct($entity)
    {
        $this->conn = Database::getConnection();
        $this->entityRepository = EntityManager::getRepositoryByEntity($entity);
        $this->entityData = EntityManager::getRepositoryData($this->entityRepository::class);
        $this->table = $this->entityData['table'];
        $this->entity = $entity;
    }

    protected function __clone()
    {
    }

    public function __wakeup()
    {
        throw new Exception("Cannot unserialize Repository class");
    }

    public function findById(string|int $id): array|bool
    {
        $dbh = $this->conn->prepare('SELECT * FROM' . $this->table . 'WHERE id = :id');

        $dbh->bindValue(':id', $id, PDO::PARAM_INT);

        return $dbh->fetchAll();
    }

    public function getInstance()
    {
        if (!isset($this->instance)) {
            // Note that here we use the "static" keyword instead of the actual
            // class name. In this context, the "static" keyword means "the name
            // of the current class". That detail is important because when the
            // method is called on the subclass, we want an instance of that
            // subclass to be created here.

            $this->instance = new static($this->entity);
        }

        return $this->instance;
    }

    public function fetchAll()
    {
        return $this->conn
            ->query('SELECT * FROM ' . $this->table)
            ->fetchAll();
    }
}
