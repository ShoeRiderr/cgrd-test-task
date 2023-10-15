<?php

namespace Exception\Database;

use Exception;

class NoDatabaseDriverException extends Exception
{
    function __construct(?string $dbDriver)
    {
        parent::__construct($this->format($dbDriver));
    }

    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function format(?string $dbDriver): string
    {
        return "Database " . $dbDriver . " driver not exists.";
    }
}
