<?php

namespace App\Infrastructure\Api\Post\CreatePost;

use App\Application\Post\CreatePost\CreatePostCommand;
use App\Application\Post\CreatePost\CreatePostUseCase;
use App\Domain\Post\Exceptions\InvalidPostDataException;
use App\Infrastructure\Shared\Bus\Command\CommandHandlerInterface;

class CreatePostCommandHandler implements CommandHandlerInterface
{
    private CreatePostUseCase $createPostUseCase;

    /**
     * @param CreatePostUseCase $createPostUseCase
     */
    public function __construct(CreatePostUseCase $createPostUseCase)
    {
        $this->createPostUseCase = $createPostUseCase;
    }

    /**
     * @param CreatePostCommand $command
     *
     * @return void
     * @throws InvalidPostDataException
     */
    public function __invoke(CreatePostCommand $command): void
    {
        $this->createPostUseCase->create($command);
    }
}
