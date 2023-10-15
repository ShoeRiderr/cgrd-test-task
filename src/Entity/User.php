<?php

namespace App\Entity;

use App\Handler\Entity\Attribute\Entity;
use App\Repository\UserRepository;

#[Entity(UserRepository::class, 'users')]
class User
{
    public $id;
    public $name;
    public $email;
    public $password;
}