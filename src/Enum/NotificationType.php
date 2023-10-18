<?php

declare(strict_types=1);

namespace App\Enum;

enum NotificationType: string
{
    case ERROR = 'error';
    case SUCCESS = 'success';
}