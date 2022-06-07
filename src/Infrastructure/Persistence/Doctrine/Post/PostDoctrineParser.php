<?php

namespace App\Infrastructure\Persistence\Doctrine\Post;

use App\Domain\Post\Post;

use App\Infrastructure\Persistence\Doctrine\Post\Post as PostEntity;
use Symfony\Component\Uid\Uuid;

class PostDoctrineParser
{
    public function toDomain(PostEntity $postEntity): Post
    {
        return new Post(
            $postEntity->getTitle(),
            $postEntity->getContent(),
            $postEntity->getPublishedAt(),
            $postEntity->getId()->__toString()
        );
    }

    public function toDoctrine(Post $post): PostEntity
    {
        $postEntity = new PostEntity(Uuid::isValid($post->getUuid()) ? Uuid::fromString($post->getUuid()) : null);

        $postEntity
            ->setTitle($post->getTitle())
            ->setContent($post->getContent())
            ->setPublishedAt($post->getPublishedAt());

        return $postEntity;
    }
}
