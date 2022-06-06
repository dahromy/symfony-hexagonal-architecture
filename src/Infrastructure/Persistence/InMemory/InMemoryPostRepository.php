<?php

namespace App\Infrastructure\Persistence\InMemory;

use App\Domain\Blog\Post;
use App\Domain\Blog\Services\PostRepositoryInterface;

class InMemoryPostRepository implements PostRepositoryInterface
{
    protected array $posts = [];

    public function save(Post $post)
    {
        $this->posts[$post->getUuid()] = $post;
    }

    public function findOneByUuid(string $uuid): ?Post
    {
        return $this->posts[$uuid] ?? null;
    }
}
