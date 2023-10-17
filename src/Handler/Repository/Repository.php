<?php

declare(strict_types=1);

namespace App\Handler\Repository;

use App\App;
use App\Exception\Entity\EntityInstatiationException;
use App\Exception\Repository\NoColumnException;
use App\Handler\Database\Database;
use App\Handler\Entity\AbstractEntity;
use App\Handler\Entity\EntityManager;
use PDO;

abstract class Repository
{
    protected Database $conn;
    protected string $entity = '';
    protected string $table = '';

    protected array $guardedCols = [];
    protected array $notGuardedCols = [];
    protected array $allCols = [];
    /**
     * Associative array with column name as a key and property name as a value
     */
    protected array $entityProps = [];

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

    public function findById(string|int $id): ?AbstractEntity
    {
        $dbh = $this->conn->prepare('SELECT ' . implode(',', $this->notGuardedCols) .  ' FROM ' . $this->table . ' WHERE id = :id');

        $dbh->bindValue(':id', $id, PDO::PARAM_INT);

        $dbh->execute();

        $result = $dbh->fetch();

        return $this->getOneEntityFromArray($result);
    }

    /**
     * @return null|AbstractEntity[]
     */
    public function findBy(string $column, mixed $value): ?array
    {
        $this->validateColumn($column);

        $dbh = $this->conn->prepare('SELECT ' . implode(',', $this->notGuardedCols) .  ' FROM ' . $this->table . ' WHERE ' . $column . ' = :' . $column);

        $dbh->bindValue($column, $value);

        $dbh->execute();

        $result =  $dbh->fetchAll() ?? [];

        return $this->getCollectionEntityFromArray($result);
    }

    public function findOneBy(string $column, mixed $value): ?AbstractEntity
    {
        $this->validateColumn($column);

        $dbh = $this->conn->prepare('SELECT ' . implode(',', $this->notGuardedCols) .  ' FROM ' . $this->table . ' WHERE ' . $column . ' = :' . $column);

        $dbh->bindValue($column, $value);

        $dbh->execute();

        $result = $dbh->fetch();

        return $this->getOneEntityFromArray($result);
    }

    /**
     * @return null|AbstractEntity[]
     */
    public function findAll(): ?array
    {
        $result = $this->conn
            ->query('SELECT ' . implode(',', $this->notGuardedCols) . ' FROM ' . $this->table)
            ->fetchAll();

        return $this->getCollectionEntityFromArray($result);
    }

    protected function validateColumn($column): bool
    {
        if (!in_array($column, $this->allCols)) {
            throw new NoColumnException($column, $this->table);
        }

        return true;
    }

    protected function getOneEntityFromArray(array $result): ?AbstractEntity
    {
        try {
            $entity = new $this->entity();

            return $entity->fromArrayToOneObject($this->entityProps, $result);
        } catch (EntityInstatiationException) {
            return null;
        }
    }

    /**
     * @return null|AbstractEntity[]
     */
    protected function getCollectionEntityFromArray(array $result): ?array
    {
        try {
            $entity = new $this->entity();

            return $entity->fromArrayToCollectionObject($this->entityProps, $result);
        } catch (EntityInstatiationException) {
            return null;
        }
    }
}
