<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\LoginDTO;
use App\Exception\Security\BadCredentialsException;
use App\Repository\UserRepository;

class SecurityService
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    /**
     * @throws BadCredentialsException
     */
    public function login(LoginDTO $data): bool
    {
        $user = $this->userRepository->findOneByNameWithPassword($data->name);

        if (!($user && isset($user['password']) && password_verify($data->password, $user['password']))) {
            throw new BadCredentialsException();
        }

        $_SESSION['name'] = $user['name'];
        $_SESSION['id'] = $user['id'];

        return true;
    }
}
