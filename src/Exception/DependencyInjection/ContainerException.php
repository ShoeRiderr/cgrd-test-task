<?php

declare(strict_types=1);

namespace App\Exception\DependencyInjection;

use Exception;
use Psr\Container\ContainerExceptionInterface;

class ContainerException extends Exception implements ContainerExceptionInterface
{
}
