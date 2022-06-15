<?php

namespace App\Infrastructure\Persistence\InMemory\Post;

use App\Domain\Post\Post;
use App\Domain\Post\Services\PostRepositoryInterface;

class InMemoryPostRepository implements PostRepositoryInterface
{
    protected array $posts = [];

    public function save(Post $post): void
    {
        $this->posts[$post->getId()] = $post;
    }

    public function findOneById(string $id): ?Post
    {
        return $this->posts[$id] ?? NULL;
    }
}
