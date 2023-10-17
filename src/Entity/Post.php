<?php

declare(strict_types=1);

namespace App\Entity;

use App\Handler\Entity\AbstractEntity;
use App\Handler\Entity\Attribute\Entity;
use App\Handler\Entity\Attribute\Property;
use App\Repository\PostRepository;

#[Entity(repositoryClass: PostRepository::class, table: 'posts')]
class Post extends AbstractEntity
{
    #[Property()]
    protected ?int $id;

    #[Property(name: 'user_id')]
    protected ?int $user;

    #[Property]
    protected ?string $title;

    #[Property]
    protected ?string $description;

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