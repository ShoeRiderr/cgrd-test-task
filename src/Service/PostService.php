<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\PostDTO;
use App\Entity\Post;
use App\Repository\PostRepository;

class PostService
{
    public function __construct(private PostRepository $postRepository)
    {
    }

    public function create(PostDTO $data): bool
    {
        $post = new Post();
        $post->setUser($data->user);
        $post->setTitle($data->title);
        $post->setDescription($data->description);

        return $this->postRepository->handleEntity($post);
    }

    public function update(PostDTO $data, Post $post): bool
    {
        $post->setUser($data->user);
        $post->setTitle($data->title);
        $post->setDescription($data->description);

        return $this->postRepository->handleEntity($post);
    }

    public function delete(int $id): bool
    {
        return $this->postRepository->delete($id);
    }
}
