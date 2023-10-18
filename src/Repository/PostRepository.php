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

    public function update(array $data): bool
    {
        $sql = "UPDATE $this->table SET user_id=:user_id, title=:title, description=:description WHERE id=:id";

        return $this->conn->prepare($sql)->execute($data);
    }
}
