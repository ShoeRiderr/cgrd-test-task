<?php

declare(strict_types=1);

// Here you add Controllers with defined routes as a method attributes

use App\Controller\SecurityController;
use App\Controller\PostController;

return [
    SecurityController::class,
    PostController::class,
];
