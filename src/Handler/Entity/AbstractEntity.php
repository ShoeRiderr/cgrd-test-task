<?php

declare(strict_types=1);

namespace App\Handler\Entity;

use App\Exception\Entity\EntityInstatiationException;
use Throwable;

abstract class AbstractEntity
{
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
}
