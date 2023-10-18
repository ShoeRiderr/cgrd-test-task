<?php

declare(strict_types=1);

namespace App\Validation;

use App\DTO\PostDTO;
use Throwable;

class PostValidation
{
    public function validate(): ?PostDTO
    {
        try {
            return new PostDTO(
                $_SESSION['id'],
                $_POST['title'],
                $_POST['description'],
            );
        } catch (Throwable $e) {
            error_log($e->getMessage());

            return null;
        }
    }
}
