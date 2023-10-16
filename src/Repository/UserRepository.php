<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use App\Handler\Repository\Repository;

class UserRepository extends Repository
{
    public function __construct()
    {
        parent::__construct(User::class);
    }
}
