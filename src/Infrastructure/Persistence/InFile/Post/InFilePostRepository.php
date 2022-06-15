<?php


namespace App\Infrastructure\Persistence\InFile\Post;


use App\Domain\Post\Post;
use App\Domain\Post\Services\PostRepositoryInterface;
use App\Infrastructure\Persistence\InFile\FilesystemHandler;
use Exception;

class InFilePostRepository implements PostRepositoryInterface
{
    private FilesystemHandler $filesystemHandler;
    private InFilePostParser $fileParser;

    /**
     * NeighbourInFileRepository constructor.
     *
     * @param FilesystemHandler $filesystemHandler
     * @param InFilePostParser  $fileParser
     */
    public function __construct(FilesystemHandler $filesystemHandler, InFilePostParser $fileParser)
    {
        $this->filesystemHandler = $filesystemHandler;
        $this->fileParser = $fileParser;
    }

    public function save(Post $post): void
    {
        $fileContent = $this->fileParser->toInFile($post);
        $this->filesystemHandler->createFile($post->getUuid(), $fileContent);
    }

    /**
     * @throws Exception
     */
    public function findOneByUuid(string $uuid): ?Post
    {
        $fileContent = $this->filesystemHandler->readFile($uuid);
        if (is_null($fileContent)) return NULL;

        return $this->fileParser->toDomain($fileContent);
    }
}
