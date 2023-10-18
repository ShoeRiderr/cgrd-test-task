<?php

declare(strict_types=1);

namespace App\Handler\Entity;

use App\Exception\Entity\EntityInstatiationException;
use App\Handler\Entity\Attribute\Property;
use Throwable;

abstract class AbstractEntity
{
    #[Property]
    protected ?int $id = null;

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @throws EntityInstatiationException
     */
    public function fromArrayToOneObject(array $entityProps, array $result): self
    {
        try {
            $newInstance = new $this;

            foreach ($result as $key => $value) {
                $newInstance->{$entityProps[$key]} = $value;
            }

            return $newInstance;
        } catch (Throwable $e) {
            error_log($e->getMessage());

            throw new EntityInstatiationException($this::class);
        }
    }

    /**
     * @return null|self[]
     */
    public function fromArrayToCollectionObject(array $entityProps, array $result): ?array
    {
        return array_map(fn ($value) => $this->fromArrayToOneObject($entityProps, $value), $result);
    }

    public function toArray(array $entityProps): array
    {
        $result = [];
        foreach ($entityProps as $key => $value) {
            $result[$key] = $this->{$value};
        }

        return $result;
    }
}
