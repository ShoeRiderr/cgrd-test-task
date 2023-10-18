<?php

declare(strict_types=1);

namespace App\Handler;

use App\Exception\DependencyInjection\ContainerException;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    /**
     * Array of class instances included in PSR 11 container.
     */
    private array $entries = [];

    public function get(string $class)
    {
        if ($this->has($class)) {
            $entry = $this->entries[$class];

            return $entry($this);
        }

        return $this->resolve($class);
    }

    public function has(string $class): bool
    {
        return isset($this->entries[$class]);
    }

    public function set(string $class, callable $concrete): void
    {
        $this->entries[$class] = $concrete;
    }

    /**
     * Resolve classes inside container.
     * Method resolves successfuly classes without constructor, with constructor but without
     * or with name typed (definded classes) parameters.
     *
     * Method not allow union type parameters.
     */
    public function resolve(string $class): mixed
    {
        // 1. Inspect the class that we are trying to get from the container
        $reflectionClass = new \ReflectionClass($class);

        if (!$reflectionClass->isInstantiable()) {
            throw new ContainerException('Class "' . $class . '" is not instantiable');
        }

        // 2. Inspect the constructor of the class
        $constructor = $reflectionClass->getConstructor();

        if (!$constructor) {
            return new $class;
        }

        // 3. Inspect the constructor parameters (dependencies)
        $parameters = $constructor->getParameters();

        if (!$parameters) {
            return new $class;
        }

        // 4. If the constructor parameter is a class then try to resolve that class using the container
        $dependencies = array_map(
            function (\ReflectionParameter $param) use ($class) {
                $name = $param->getName();
                $type = $param->getType();

                if (!$type) {
                    throw new ContainerException(
                        'Failed to resolve class "' . $class . '" because param "' . $name . '" is missing a type hint'
                    );
                }

                // Union types are not allowed in class constructors 
                if ($type instanceof \ReflectionUnionType) {
                    throw new ContainerException(
                        'Failed to resolve class "' . $class . '" because of union type for param "' . $name . '"'
                    );
                }

                if ($type instanceof \ReflectionNamedType && !$type->isBuiltin()) {
                    return $this->get($type->getName());
                }

                throw new ContainerException(
                    'Failed to resolve class "' . $class . '" because invalid param "' . $name . '"'
                );
            },
            $parameters
        );

        return $reflectionClass->newInstanceArgs($dependencies);
    }
}
