<?php

namespace App\Domain\Post\Services;

use App\Domain\Post\Post;

interface PostRepositoryInterface
{
    public function save(Post $post) : Post;

    public function findOneByUuid(string $uuid) : ?Post;
}
