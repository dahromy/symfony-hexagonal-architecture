<?php


namespace App\Infrastructure\Post\Repository;


use App\Domain\Post\Post;
use App\Domain\Post\Repository\PostRepositoryInterface;
use App\Infrastructure\Bridge\InFile\FilesystemHandler;
use App\Infrastructure\Post\InFile\InFilePostParser;
use Exception;

class InFilePostRepository implements PostRepositoryInterface
{
    private FilesystemHandler $filesystemHandler;
    private InFilePostParser $fileParser;

    /**
     * NeighbourInFileRepository constructor.
     *
     * @param \App\Infrastructure\Bridge\InFile\FilesystemHandler $filesystemHandler
     * @param InFilePostParser $fileParser
     */
    public function __construct(FilesystemHandler $filesystemHandler, InFilePostParser $fileParser)
    {
        $this->filesystemHandler = $filesystemHandler;
        $this->fileParser = $fileParser;
    }

    public function save(Post $post): void
    {
        $fileContent = $this->fileParser->toInFile($post);
        $this->filesystemHandler->createFile($post->getId(), $fileContent);
    }

    /**
     * @throws Exception
     */
    public function findOneById(string $id): ?Post
    {
        $fileContent = $this->filesystemHandler->readFile($id);
        if (is_null($fileContent)) return NULL;

        return $this->fileParser->toDomain($fileContent);
    }
}
