<?php

declare(strict_types=1);

namespace App\Handler\Entity;

use App\Exception\Repository\RepositoryNotAttachedToAnyEntity;
use App\Handler\Entity\Attribute\Entity;
use App\Handler\Entity\Attribute\Property;
use App\Handler\Repository\Repository;
use App\Handler\Util\ClassFinder;
use Exception;
use ReflectionMethod;
use ReflectionProperty;

final class EntityManager
{
    private static array $repositories = [];
    private static array $entities = [];

    public function __construct()
    {
        self::matchWithRepositories();
    }

    final public static function matchWithRepositories()
    {
        $entities = ClassFinder::getClassesInNamespace('App\Entity');

        foreach ($entities as $entity) {
            $reflectionEntity = new \ReflectionClass($entity);

            $entityAttributes = $reflectionEntity->getAttributes(Entity::class);

            self::handleEntityAttributes($entityAttributes, $entity);

            $entityProps = $reflectionEntity->getProperties(ReflectionProperty::IS_PROTECTED);

            [
                'properties' => $properties,
                'allColumns' => $allColumns,
                'guardedColumns' => $guardedColumns,
                'notGuardedColumns' => $notGuardedColumns,
            ] = self::handleEntityProperties($entityProps);

            self::$repositories[self::$entities[$entity]]['properties'] = $properties;
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

    /**
     * @param ReflectionProperty[] $entityProps
     */
    private static function handleEntityProperties(array $entityProps): array
    {
        // Associative array with column name as a key and property name as a value
        $properties = [];
        $allColumns = [];
        $guardedColumns = [];
        $notGuardedColumns = [];

        foreach ($entityProps as $entityProp) {
            $propertyAttributes = $entityProp->getAttributes(Property::class);
            $propName = $entityProp->getName();

            foreach ($propertyAttributes as $propertyAttribute) {
                $propertyAttributesClass = $propertyAttribute->newInstance();
                $colName = $propertyAttributesClass->name ?? $propName;
                $properties[$colName] = $propName;

                $allColumns[] = $colName;

                if ($propertyAttributesClass->guarded) {
                    $guardedColumns[] = $colName;
                } else {
                    $notGuardedColumns[] = $colName;
                }
            }
        }

        return [
            'properties' => $properties,
            'allColumns' => $allColumns,
            'guardedColumns' => $guardedColumns,
            'notGuardedColumns' => $notGuardedColumns,
        ];
    }

    /**
     * @param ReflectionAttribute<T>[] $entityAttrs
     */
    private static function handleEntityAttributes(array $entityAttrs, string $entity): void
    {
        foreach ($entityAttrs as $entityAttribute) {
            $attributesClass = $entityAttribute->newInstance();

            $repositoryClass = $attributesClass->repositoryClass;

            self::$repositories[$repositoryClass] = [
                'class'  => $entity,
                'table'  => $attributesClass->table,
            ];

            self::$entities[$entity] = $repositoryClass;
        }
    }
}
