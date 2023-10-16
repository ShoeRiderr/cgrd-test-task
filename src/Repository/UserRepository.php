<?php

namespace App\Repository;

use App\Entity\User;
use App\Handler\Container;
use App\Handler\Repository\Repository;

class UserRepository extends Repository
{
    public function __construct()
    {
        parent::__construct(User::class, new Container());
    }
}