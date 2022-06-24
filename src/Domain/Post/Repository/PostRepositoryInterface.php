<?php

namespace App\Domain\Post\Repository;

use App\Domain\Post\Post;
use Symfony\Component\Uid\Uuid;

interface PostRepositoryInterface
{
    public function save(Post $post): void;

    public function findOneById(Uuid $id): ?Post;
}
