<?php

namespace App\Application\UseCase\Command\Post\Create;

use App\Domain\Post\Exception\InvalidPostDataException;
use App\Domain\Post\Post;
use App\Domain\Post\Repository\PostRepositoryInterface;
use App\Domain\Shared\IdGenerator;
use Assert\LazyAssertionException;
use DateTimeInterface;
use Symfony\Component\Uid\Uuid;
use function Assert\lazy;

class CreatePostUseCase
{
    private PostRepositoryInterface $postRepository;
    private IdGenerator $idGenerator;

    /**
     * @param PostRepositoryInterface $postRepository
     * @param IdGenerator $idGenerator
     */
    public function __construct(PostRepositoryInterface $postRepository, IdGenerator $idGenerator)
    {
        $this->postRepository = $postRepository;
        $this->idGenerator = $idGenerator;
    }

    /**
     * @param CreatePostCommand $command
     *
     * @return CreatePostResponse
     * @throws InvalidPostDataException
     */
    public function create(CreatePostCommand $command): CreatePostResponse
    {
        $id = $this->generatePostId();

        $post = new Post($id, $command->getTitle(), $command->getContent(), $command->getPublishedAt());

        try {
            $this->validate($post);

            $this->postRepository->save($post);

            return new CreatePostResponse($post);
        } catch (LazyAssertionException $e) {
            throw new InvalidPostDataException($e->getMessage());
        }
    }

    /**
     * @return Uuid
     */
    private function generatePostId(): Uuid
    {
        $maxAttempts = 5;
        $attempts = 0;

        $id = $this->idGenerator->generate();

        while ($attempts < $maxAttempts && !is_null($this->postRepository->findOneById($id))) {

            $id = $this->idGenerator->generate();

            $attempts++;
            if ($attempts >= $maxAttempts) {
                // throw new IdGenerationAttemptsExceeded($maxAttempts);
            }
        }

        return $id;
    }

    /**
     * @param Post $post
     *
     * @return void
     */
    protected function validate(Post $post): void
    {
        lazy()
            ->that($post->getTitle())->notBlank()->minLength(3)
            ->that($post->getContent())->notBlank()->minLength(10)
            ->that($post->getPublishedAt())->nullOr()->isInstanceOf(DateTimeInterface::class)
            ->verifyNow();
    }
}
