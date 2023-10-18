<?php

declare(strict_types = 1);

namespace App\Entity;

use App\Handler\Entity\AbstractEntity;
use App\Handler\Entity\Attribute\Entity;
use App\Repository\UserRepository;
use App\Handler\Entity\Attribute\Property;

#[Entity(repositoryClass: UserRepository::class, table: 'users')]
class User extends AbstractEntity
{
    #[Property]
    protected ?string $name;

    #[Property]
    protected ?string $email;

    #[Property(guarded: true)]
    protected ?string $password;

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}