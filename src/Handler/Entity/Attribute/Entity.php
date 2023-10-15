<?php

declare(strict_types=1);

namespace App\Handler\Entity\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class Entity
{
    /**
     * @var string
     */
    public string $repositoryClass;

    /**
     * @var string
     */
    public string $table;

    public function __construct(string $repositoryClass, string $table)
    {
        $this->repositoryClass = $repositoryClass;
        $this->table = $table;
    }
}
