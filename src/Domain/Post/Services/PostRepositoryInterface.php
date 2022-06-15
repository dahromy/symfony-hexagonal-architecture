<?php

namespace App\Domain\Post\Services;

use App\Domain\Post\Post;

interface PostRepositoryInterface
{
    public function save(Post $post): void;

    public function findOneById(string $id): ?Post;
}
