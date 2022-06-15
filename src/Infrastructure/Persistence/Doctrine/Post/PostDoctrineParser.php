<?php

namespace App\Infrastructure\Persistence\Doctrine\Post;

use App\Domain\Post\Post;
use App\Infrastructure\Persistence\Doctrine\Post\Post as PostEntity;
use Symfony\Component\Uid\Uuid;

class PostDoctrineParser
{
    /**
     * @param PostEntity $postEntity
     *
     * @return Post
     */
    public function toDomain(PostEntity $postEntity): Post
    {
        return new Post(
            $postEntity->getId() ? $postEntity->getId()->toRfc4122() : '',
            $postEntity->getTitle() ?? '',
            $postEntity->getContent() ?? '',
            $postEntity->getPublishedAt(),
        );
    }

    public function toDoctrine(Post $post): PostEntity
    {
        $postEntity = new PostEntity(Uuid::v6()::fromString($post->getId()));

        $postEntity
            ->setTitle($post->getTitle())
            ->setContent($post->getContent())
            ->setPublishedAt($post->getPublishedAt());

        return $postEntity;
    }
}
