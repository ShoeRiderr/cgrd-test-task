<?php

namespace App\Validation;

use App\DTO\LoginDTO;
use Throwable;

class LoginValidation
{
    public function validate(): ?LoginDTO
    {
        try {
            return new LoginDTO(
                $_POST['name'],
                $_POST['password'],
            );
        } catch (Throwable $e) {
            error_log($e->getMessage());

            return null;
        }
    }
}