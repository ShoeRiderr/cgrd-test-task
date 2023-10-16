<?php

namespace App\Handler\Repository;

use App\App;
use App\Handler\Container;
use App\Handler\Entity\EntityManager;
use Exception;
use PDO;

abstract class Repository
{
    protected $conn = '';
    protected string $entity = '';
    protected string $table = '';
    protected array $repositoryData = [];
    protected EntityManager $entityManager;

    public function __construct(string $entity, private Container $container)
    {
        $this->entity = $entity;
        $this->conn = App::db();

        $this->entityManager = $container->get(EntityManager::class);
        $this->repositoryData = $this->entityManager->getRepositoryData($this::class);
        $this->table = $this->repositoryData['table'] ?? '';
    }

    public function findById(string|int $id): array|bool
    {
        $dbh = $this->conn->prepare('SELECT * FROM' . $this->table . 'WHERE id = :id');

        $dbh->bindValue(':id', $id, PDO::PARAM_INT);

        return $dbh->fetchAll();
    }

    public function fetchAll(): array|false
    {
        return $this->conn
            ->query('SELECT * FROM ' . $this->table)
            ->fetchAll();
    }
}
