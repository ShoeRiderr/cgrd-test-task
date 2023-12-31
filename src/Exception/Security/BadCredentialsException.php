<?php

declare(strict_types=1);

namespace App\Exception\Security;

use Exception;

class BadCredentialsException extends Exception
{
    protected $message = "Bad credentials";
}
