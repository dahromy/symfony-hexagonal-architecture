<?php


namespace App\Infrastructure\Persistence\InFile\Post;


use App\Domain\Post\Post;
use DateTime;
use Exception;

class InFilePostParser
{
    /**
     * @throws Exception
     */
    public function toDomain(string $fileContent): Post
    {
        $postParts = preg_split('/~/', $fileContent);
        return new Post($postParts[0], $postParts[1], $postParts[2], new DateTime($postParts[3]));
    }

    public function toInFile(Post $post): string
    {
        return "{$post->getId()}~{$post->getTitle()}~{$post->getContent()}~{$post->getPublishedAt()->format('YmdHis')}";
    }
}
