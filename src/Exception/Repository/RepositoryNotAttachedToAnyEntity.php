<?php

namespace App\Exception\Repository;

use Exception;

class RepositoryNotAttachedToAnyEntity extends Exception
{
    function __construct(string $repository)
    {
        parent::__construct($this->format($repository));
    }

    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function format(string $repository): string
    {
        return $repository . " class is not existing or is not assigned with repository or entity.";
    }
}