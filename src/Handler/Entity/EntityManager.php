<?php

namespace App\Handler\Entity;

use App\Exception\Repository\RepositoryNotAttachedToAnyEntity;
use App\Handler\Entity\Attribute\Entity;
use App\Handler\Repository\Repository;
use App\Handler\Util\ClassFinder;
use Exception;

final class EntityManager
{
    private static $instance = '';
    private static array $repositories;
    private static array $entities;

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
