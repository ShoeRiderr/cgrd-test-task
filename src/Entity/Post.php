<?php

declare(strict_types=1);

namespace App\Entity;

use App\Handler\Entity\Attribute\Entity;
use App\Handler\Entity\Attribute\Property;
use App\Repository\PostRepository;

#[Entity(repositoryClass: PostRepository::class, table: 'posts')]
class Post
{
    #[Property()]
    private int $id;

    #[Property(name: 'user_id')]
    private int $user;

    #[Property]
    private string $title;

    #[Property]
    private string $description;

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setUser(int $user): void
    {
        $this->user = $user;
    }

    public function getUser(): int
    {
        return $this->user;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}