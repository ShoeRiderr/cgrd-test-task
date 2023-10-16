<?php

declare(strict_types=1);

namespace App\DTO;

class LoginDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $password,
    ) {
    }
}
