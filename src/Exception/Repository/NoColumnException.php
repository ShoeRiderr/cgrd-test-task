<?php

declare(strict_types=1);

namespace App\Exception\Repository;

use Exception;

class NoColumnException extends Exception
{
    function __construct(string $column, string $table)
    {
        parent::__construct($this->format($column, $table));
    }

    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function format(string $column, $table): string
    {
        return "Column " . $column . " is not present in " . $table;
    }
}
