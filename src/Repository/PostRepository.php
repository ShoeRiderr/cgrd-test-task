<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Post;
use App\Handler\Repository\Repository;

class PostRepository extends Repository
{
    public function __construct()
    {
        parent::__construct(Post::class);
    }
}
