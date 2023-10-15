<?php

namespace App\Exception\Entity;

use Exception;

class NoEntityException extends Exception
{
    protected $message = "Entities are not defined.";
}
