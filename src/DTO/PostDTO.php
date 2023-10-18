<?php

declare(strict_types=1);

namespace App\DTO;

class PostDTO
{
    public function __construct(
        public readonly ?int $user,
        public readonly string $title,
        public readonly string $description,
    ) {
    }
}
