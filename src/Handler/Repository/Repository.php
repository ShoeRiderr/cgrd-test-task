<?php

declare(strict_types=1);

namespace App\Handler\Repository;

use App\App;
use App\Exception\Entity\EntityInstatiationException;
use App\Exception\Repository\NoColumnException;
use App\Handler\Database\Database;
use App\Handler\Entity\AbstractEntity;
use App\Handler\Entity\EntityManager;
use App\Handler\Repository\Trait\QueryParamHelper;

abstract class Repository
{
    use QueryParamHelper;

    /**
     * Associative array with column name as a key and property name as a value
     */
    protected array $entityProps = [];
    protected array $queryResult = [];

    protected Database $conn; // PDO connection
    protected string $entity = ''; // Repository entity
    protected string $table = ''; // Entity class

    // Sql query helpers
    protected array $guardedCols = [];
    protected array $notGuardedCols = [];
    public array $allCols = [];

    protected array $entityData = []; // Entity columns and properties for sql query use
    protected EntityManager $entityManager; // EntityManager instance

    public function __construct(string $entity)
    {
        $this->entity = $entity;
        $this->conn = App::db();

        $this->entityManager = App::entityManager();
        $this->entityData = $this->entityManager->getEntityData($this::class);
        $this->table = $this->entityData['table'] ?? '';
        $this->guardedCols = $this->entityData['columns']['guarded'] ?? [];
        $this->notGuardedCols = $this->entityData['columns']['notGuarded'] ?? [];
        $this->entityProps = $this->entityData['properties'] ?? [];
        $this->allCols = array_keys($this->entityData['properties']) ?? [];
    }

    public function findById(string|int $id): self
    {
        $this->findOneBy('id', $id);

        return $this;
    }

    public function findBy(string $column, mixed $value): self
    {
        $result = $this->executeFindBy([$column => $value])->fetchAll();

        $this->queryResult = !$result ? [] : $result;

        return $this;
    }

    public function findOneBy(string $column, mixed $value): self
    {
        $result = $this->executeFindBy([$column => $value])->fetch();

        $this->queryResult = !$result ? [] : $result;

        return $this;
    }

    public function findAll(): self
    {
        $sql = 'SELECT ' . implode(',', $this->notGuardedCols) . ' FROM ' . $this->table;

        $this->queryResult = $this->conn
            ->query($sql)
            ->fetchAll() ?? [];

        return $this;
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM " . $this->table . " WHERE id=?";

        return $this->conn
            ->prepare($sql)
            ->execute([$id]);
    }

    /**
     * Method handles given entity object and create or update record in database
     */
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
        ['columns' => $columns, 'values' => $values] = $this->prepareForCreateParamBinding($data);

        $sql = "INSERT INTO $this->table ($columns) VALUES ($values)";

        return $this->conn
            ->prepare($sql)
            ->execute(array_values($data));
    }

    public function update(array $data): bool
    {
        $columns = $this->prepareForUpdateParamBinding($data);

        $sql = "UPDATE $this->table SET $columns WHERE id=:id";

        return $this->conn->prepare($sql)->execute($data);
    }

    public function getObject(): array
    {
        if (is_array($this->queryResult[0])) {
            return $this->getCollectionEntityFromArray();
        }

        return $this->getOneEntityFromArray();
    }

    public function getArray(): array
    {
        return $this->queryResult;
    }

    public function getOneEntityFromArray(): ?AbstractEntity
    {
        try {
            $entity = new $this->entity();

            return $entity->fromArrayToOneObject($this->entityProps, $this->queryResult);
        } catch (EntityInstatiationException) {
            return null;
        }
    }

    public function validateColumn($column): bool
    {
        if (!in_array($column, $this->allCols)) {
            throw new NoColumnException($column, $this->table);
        }

        return true;
    }

    public function validateColumns(array $columns)
    {
        foreach ($columns as $column) {
            $this->validateColumn($column);
        }

        return true;
    }

    /**
     * Associative array with values attached to column name as a key 
     * @param array $columnValue
     */
    protected function executeFindBy(array $columnValue)
    {
        $columns = array_keys($columnValue);
        $this->validateColumns($columns);

        $whereClause = $this->prepereWhereClause($columns);
        $sql = 'SELECT ' . implode(',', $this->notGuardedCols) .  ' FROM ' . $this->table . $whereClause;

        $builder = $this->conn->prepare($sql);

        foreach ($columnValue as $column => $value) {
            $builder->bindValue($column, $value);
        }

        $builder->execute();

        return $builder;
    }

    protected function prepereWhereClause(array $columns): string
    {
        $result = array_map(function ($column, $index) {
            if ($index > 0) {
                return " AND $column = :$column";
            }

            return " WHERE $column = :$column";
        }, $columns, array_keys($columns));

        return implode(', ', $result);
    }

    /**
     * @return null|AbstractEntity[]
     */
    protected function getCollectionEntityFromArray(): ?array
    {
        try {
            $entity = new $this->entity();

            return $entity->fromArrayToCollectionObject($this->entityProps, $this->queryResult);
        } catch (EntityInstatiationException) {
            return null;
        }
    }
}
