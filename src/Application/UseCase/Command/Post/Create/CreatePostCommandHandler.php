<?php

namespace App\Application\UseCase\Command\Post\Create;

use App\Domain\Post\Exception\InvalidPostDataException;
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
