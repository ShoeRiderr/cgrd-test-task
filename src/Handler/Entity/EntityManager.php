<?php

declare(strict_types=1);

namespace App\Handler\Entity;

use App\Exception\Repository\RepositoryNotAttachedToAnyEntity;
use App\Handler\Entity\Attribute\Entity;
use App\Handler\Entity\Attribute\Property;
use App\Handler\Repository\Repository;
use App\Handler\Util\ClassFinder;
use Exception;
use ReflectionProperty;

final class EntityManager
{
    private static $instance = '';
    private static array $repositories = [];
    private static array $entities = [];

    public function __construct()
    {
        self::matchWithRepositories();
    }

    protected function __clone()
    {
    }

    public function __wakeup()
    {
        throw new Exception("Cannot unserialize EntityManager class");
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            // Note that here we use the "static" keyword instead of the actual
            // class name. In this context, the "static" keyword means "the name
            // of the current class". That detail is important because when the
            // method is called on the subclass, we want an instance of that
            // subclass to be created here.

            self::$instance = new static();
        }

        return self::$instance;
    }

    final public static function matchWithRepositories()
    {
        $entities = ClassFinder::getClassesInNamespace('App\Entity');

        foreach ($entities as $entity) {
            $reflectionEntity = new \ReflectionClass($entity);

            $entityAttributes = $reflectionEntity->getAttributes(Entity::class);

            foreach ($entityAttributes as $entityAttribute) {
                $attributesClass = $entityAttribute->newInstance();

                $repositoryClass = $attributesClass->repositoryClass;

                self::$repositories[$repositoryClass] = [
                    'class'  => $entity,
                    'table'  => $attributesClass->table,
                ];

                self::$entities[$entity] = $repositoryClass;
            }

            $entityProps = $reflectionEntity->getProperties(ReflectionProperty::IS_PRIVATE);

            $allColumns = [];
            $guardedColumns = [];
            $notGuardedColumns = [];
            foreach ($entityProps as $entityProp) {
                $propertyAttributes = $entityProp->getAttributes(Property::class);

                foreach ($propertyAttributes as $propertyAttribute) {
                    $propertyAttributesClass = $propertyAttribute->newInstance();
                    $colName = $propertyAttributesClass->name ?? $entityProp->getName();

                    $allColumns[] = $colName;

                    if ($propertyAttributesClass->guarded) {
                        $guardedColumns[] = $colName;
                    } else {
                        $notGuardedColumns[] = $colName;
                    }
                }
            }

            self::$repositories[self::$entities[$entity]]['columns']['all'] = $allColumns;
            self::$repositories[self::$entities[$entity]]['columns']['guarded'] = $guardedColumns;
            self::$repositories[self::$entities[$entity]]['columns']['notGuarded'] = $notGuardedColumns;
        }
    }

    final public static function getRepositories(): array
    {
        return self::$repositories;
    }

    /**
     * @throws RepositoryNotAttachedToAnyEntity
     */
    public static function getRepositoryByEntity(string $entity): Repository
    {
        if (!isset(self::$entities[$entity])) {
            throw new RepositoryNotAttachedToAnyEntity($entity);
        }

        return new self::$entities[$entity];
    }

    /**
     * @throws RepositoryNotAttachedToAnyEntity
     */
    final public static function getRepositoryData(string $repository): array
    {
        if (!isset(self::$repositories[$repository])) {
            throw new RepositoryNotAttachedToAnyEntity($repository);
        }

        return self::$repositories[$repository];
    }
}
