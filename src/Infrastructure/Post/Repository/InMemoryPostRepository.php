<?php

namespace App\Infrastructure\Post\Repository;

use App\Domain\Post\Post;
use App\Domain\Post\Repository\PostRepositoryInterface;
use Symfony\Component\Uid\Uuid;

class InMemoryPostRepository implements PostRepositoryInterface
{
    /** @var array<array-key, Post> */
    protected array $posts = [];

    public function save(Post $post): void
    {
        $this->posts[$post->getId()->toRfc4122()] = $post;
    }

    public function findOneById(Uuid $id): ?Post
    {
        return $this->posts[$id->toRfc4122()] ?? null;
    }
}
