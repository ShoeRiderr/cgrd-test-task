<?php

declare(strict_types = 1);

namespace App\Handler\Entity\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Property
{
    public function __construct(public readonly ?string $name = null, public readonly bool $guarded = false)
    {
    }
}
