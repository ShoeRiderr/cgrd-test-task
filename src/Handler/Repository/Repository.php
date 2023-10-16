<?php

declare(strict_types = 1);

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

    protected array $guardedCols = [];
    protected array $notGuardedCols = [];
    protected array $allCols = [];

    protected array $repositoryData = [];
    protected EntityManager $entityManager;

    public function __construct(string $entity)
    {
        $this->entity = $entity;
        $this->conn = App::db();

        $this->entityManager = App::entityManager();
        $this->repositoryData = $this->entityManager->getRepositoryData($this::class);
        $this->table = $this->repositoryData['table'] ?? '';
        $this->guardedCols = $this->repositoryData['columns']['guarded'] ?? [];
        $this->notGuardedCols = $this->repositoryData['columns']['notGuarded'] ?? [];
        $this->allCols = $this->repositoryData['columns']['all'] ?? [];
    }

    public function findById(string|int $id): array|bool
    {
        $dbh = $this->conn->prepare('SELECT ' . implode(',', $this->notGuardedCols) .  ' FROM ' . $this->table . ' WHERE id = :id');

        $dbh->bindValue(':id', $id, PDO::PARAM_INT);

        $dbh->execute();

        return $dbh->fetch();
    }

    public function fetchAll(): array|false
    {
        return $this->conn
            ->query('SELECT ' . implode(',', $this->notGuardedCols) . ' FROM ' . $this->table)
            ->fetchAll();
    }
}
