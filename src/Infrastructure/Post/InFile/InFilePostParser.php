<?php


namespace App\Infrastructure\Post\InFile;


use App\Domain\Post\Post;
use DateTime;
use Exception;
use Symfony\Component\Uid\UuidV6;

class InFilePostParser
{
    /**
     * @throws Exception
     */
    public function toDomain(string $fileContent): Post
    {
        /** @var array<string> $postParts */
        $postParts = preg_split('/~/', $fileContent);
        return new Post(UuidV6::fromString($postParts[0]), $postParts[1], $postParts[2], new DateTime($postParts[3]));
    }

    public function toInFile(Post $post): string
    {
        $publishedAt = $post->getPublishedAt() ? $post->getPublishedAt()->format('YmdHis') : null;

        $fileContent = "{$post->getId()->toRfc4122()}~{$post->getTitle()}~{$post->getContent()}";

        if ($publishedAt) {
            $fileContent .= "~$publishedAt";
        }

        return $fileContent;
    }
}
