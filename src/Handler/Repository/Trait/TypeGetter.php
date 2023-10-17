<?php

namespace App\Handler\Repository\Trait;

use App\Exception\Entity\EntityInstatiationException;
use App\Handler\Entity\AbstractEntity;

trait TypeGetter
{
    /**
     * Associative array with column name as a key and property name as a value
     */
    protected array $entityProps = [];
    protected array $queryResult = [];
    
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

    protected function getOneEntityFromArray(): ?AbstractEntity
    {
        try {
            $entity = new $this->entity();

            return $entity->fromArrayToOneObject($this->entityProps, $this->queryResult);
        } catch (EntityInstatiationException) {
            return null;
        }
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