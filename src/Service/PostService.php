<?php

declare(strict_types = 1);

namespace App\Service;

use App\Repository\PostRepository;

class PostService
{
    public function __construct(private PostRepository $postRepository)
    {}

    public function create(array $data)
    {
        // $this->postRepository
    }
}