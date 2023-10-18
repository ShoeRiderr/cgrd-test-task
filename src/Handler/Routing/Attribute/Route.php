<?php

declare(strict_types=1);

namespace App\Handler\Routing\Attribute;

#[\Attribute(\Attribute::TARGET_METHOD)]
class Route
{
    /**
     * @var array $parameters Keeps the parameters cached with the associated regex
     */
    private array $parameters = [];

    public function __construct(
        private string $path,
        private string $name = '',
        private array $methods = ['GET'],
        private bool $authRequired = false,
    ) {
        if (empty($this->name)) {
            $this->name = $this->path;
        }
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @return bool
     */
    public function getAuthRequired(): bool
    {
        return $this->authRequired;
    }
}
