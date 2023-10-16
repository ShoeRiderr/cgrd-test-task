<?php

declare(strict_types = 1);

namespace App\Entity;

use App\Handler\Entity\Attribute\Entity;
use App\Repository\UserRepository;
use App\Handler\Entity\Attribute\Property;

#[Entity(repositoryClass: UserRepository::class, table: 'users')]
class User
{
    #[Property]
    private int $id;

    #[Property]
    private string $name;

    #[Property]
    private string $email;

    #[Property(guarded: true)]
    private string $password;

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

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