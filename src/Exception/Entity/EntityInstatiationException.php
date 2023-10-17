<?php

declare(strict_types=1);

namespace App\Exception\Entity;

use Exception;

class EntityInstatiationException extends Exception
{
    function __construct(string $entity)
    {
        parent::__construct($this->format($entity));
    }

    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function format(string $entity): string
    {
        return 'Problem with instantiate ' . $entity . ' entity.';
    }
}
