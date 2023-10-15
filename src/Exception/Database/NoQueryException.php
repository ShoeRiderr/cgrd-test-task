<?php

namespace Exception\Database;

use Exception;

class NoQueryException extends Exception
{
    function __construct(string $method)
    {
        parent::__construct($this->format($method));
    }

    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function format(string $method): string
    {
        return "To use " . $method . " method you have to first set query.";
    }
}
