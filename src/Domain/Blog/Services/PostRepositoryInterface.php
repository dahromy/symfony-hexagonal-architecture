<?php

namespace App\Domain\Blog\Services;

use App\Domain\Blog\Post;

interface PostRepositoryInterface
{
    public function save(Post $post);

    public function findOneByUuid(string $uuid) : ?Post;
}
