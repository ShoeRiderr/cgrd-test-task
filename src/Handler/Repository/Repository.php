<?php

declare(strict_types=1);

namespace App\Handler\Repository;

use App\App;
use App\DTO\PostDTO;
use App\Exception\Repository\NoColumnException;
use App\Handler\Database\Database;
use App\Handler\Entity\AbstractEntity;
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
    public array $allCols = [];

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
        $this->findOneBy('id', $id);

        return $this;
    }

    public function findBy(string $column, mixed $value): self
    {
        $this->validateColumn($column);

        $dbh = $this->conn->prepare('SELECT ' . implode(',', $this->notGuardedCols) .  ' FROM ' . $this->table . ' WHERE ' . $column . ' = :' . $column);

        $dbh->bindValue($column, $value);

        $dbh->execute();

        $result =  $dbh->fetchAll();

        $this->queryResult = !$result ? [] : $result;

        return $this;
    }

    public function findOneBy(string $column, mixed $value): self
    {
        $this->validateColumn($column);

        $sql = 'SELECT ' . implode(', ', $this->notGuardedCols) .  ' FROM ' . $this->table . ' WHERE ' . $column . ' = :' . $column;

        $dbh = $this->conn->prepare($sql);

        $dbh->bindValue($column, (string)$value);

        $dbh->execute();

        $result = $dbh->fetch();

        $this->queryResult = !$result ? [] : $result;

        return $this;
    }

    public function findAll(): self
    {
        $this->queryResult = $this->conn
            ->query('SELECT ' . implode(',', $this->notGuardedCols) . ' FROM ' . $this->table)
            ->fetchAll() ?? [];

        return $this;
    }

    public function delete(int $id): bool
    {
        return $this->conn->prepare("DELETE FROM " . $this->table . " WHERE id=?")->execute([$id]);
    }

    public function handleEntity(AbstractEntity $entity): bool
    {
        /**
         * @var array $data
         */
        $data = $entity->toArray($this->entityProps);

        if (!$entity->getId()) {
            unset($data['id']);

            return $this->create($data);
        }

        return $this->update($data);
    }

    public function create(array $data): bool
    {
        $columns = array_keys($data);

        $values = str_repeat('? ', count($columns));
        $values = trim($values);
        $values = explode(' ', $values);
        $values = implode(', ', $values);

        $columns = implode(', ', $columns);

        $sql = "INSERT INTO $this->table ($columns) VALUES ($values)";

        return $this->conn->prepare($sql)->execute(array_values($data));
    }

    abstract public function update(array $data): bool;

    protected function validateColumn($column): bool
    {
        if (!in_array($column, $this->allCols)) {
            throw new NoColumnException($column, $this->table);
        }

        return true;
    }
}
