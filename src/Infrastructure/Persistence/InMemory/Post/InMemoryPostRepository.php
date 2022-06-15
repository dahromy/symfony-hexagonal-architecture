<?php

namespace App\Infrastructure\Persistence\InMemory\Post;

use App\Domain\Post\Post;
use App\Domain\Post\Services\PostRepositoryInterface;

class InMemoryPostRepository implements PostRepositoryInterface
{
    protected array $posts = [];

    public function save(Post $post): void
    {
        $this->posts[$post->getUuid()] = $post;
    }

    public function findOneByUuid(string $uuid): ?Post
    {
        return $this->posts[$uuid] ?? NULL;
    }
}
