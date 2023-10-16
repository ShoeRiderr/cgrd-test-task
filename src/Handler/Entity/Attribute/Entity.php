<?php

declare(strict_types=1);

namespace App\Handler\Entity\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class Entity
{
    public function __construct(public readonly string $repositoryClass, public readonly string $table)
    {
    }
}
