<?php

namespace App\Domain\Post\Repository;

use App\Domain\Post\Post;
use Symfony\Component\Uid\Uuid;

interface PostRepositoryInterface
{
    /**
     * @param Post $post
     * @return void
     */
    public function save(Post $post): void;

    /**
     * @param Uuid $id
     * @return Post|null
     */
    public function findOneById(Uuid $id): ?Post;
}
