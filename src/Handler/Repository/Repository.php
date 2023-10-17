<?php

declare(strict_types=1);

namespace App\Handler\Repository;

use App\App;
use App\Exception\Repository\NoColumnException;
use App\Handler\Database\Database;
use App\Handler\Entity\EntityManager;
use App\Handler\Repository\Trait\TypeGetter;
use PDO;

abstract class Repository
{
    use TypeGetter;

    protected Database $conn;
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
        $this->entityProps = $this->repositoryData['properties'] ?? [];
    }

    public function findById(string|int $id): self
    {
        $dbh = $this->conn->prepare('SELECT ' . implode(',', $this->notGuardedCols) .  ' FROM ' . $this->table . ' WHERE id = :id');

        $dbh->bindValue(':id', $id, PDO::PARAM_INT);

        $dbh->execute();

        $this->queryResult = $dbh->fetch();

        return $this;
    }

    public function findBy(string $column, mixed $value): self
    {
        $this->validateColumn($column);

        $dbh = $this->conn->prepare('SELECT ' . implode(',', $this->notGuardedCols) .  ' FROM ' . $this->table . ' WHERE ' . $column . ' = :' . $column);

        $dbh->bindValue($column, $value);

        $dbh->execute();

        $this->queryResult =  $dbh->fetchAll() ?? [];

        return $this;
    }

    public function findOneBy(string $column, mixed $value): self
    {
        $this->validateColumn($column);

        $dbh = $this->conn->prepare('SELECT ' . implode(',', $this->notGuardedCols) .  ' FROM ' . $this->table . ' WHERE ' . $column . ' = :' . $column);

        $dbh->bindValue($column, $value);

        $dbh->execute();

        $this->queryResult = $dbh->fetch();

        return $this;
    }

    public function findAll(): self
    {
        $this->queryResult = $this->conn
            ->query('SELECT ' . implode(',', $this->notGuardedCols) . ' FROM ' . $this->table)
            ->fetchAll();

        return $this;
    }

    protected function validateColumn($column): bool
    {
        if (!in_array($column, $this->allCols)) {
            throw new NoColumnException($column, $this->table);
        }

        return true;
    }
}
