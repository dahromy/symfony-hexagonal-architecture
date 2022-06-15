<?php

namespace App\Application\Post\CreatePost;

use App\Domain\Post\Exceptions\InvalidPostDataException;
use App\Domain\Post\Post;
use App\Domain\Post\Services\PostRepositoryInterface;
use Assert\LazyAssertionException;
use DateTimeInterface;
use function Assert\lazy;

class CreatePostUseCase
{
    private PostRepositoryInterface $postRepository;

    /**
     * @param PostRepositoryInterface $postRepository
     */
    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * @throws InvalidPostDataException
     */
    public function execute(CreatePostCommand $createPostCommand): CreatePostResponse
    {
        $post = new Post($createPostCommand->getTitle(), $createPostCommand->getContent(), $createPostCommand->getPublishedAt());

        try {
            $this->validate($post);

            $this->postRepository->save($post);

            return new CreatePostResponse($post);
        } catch (LazyAssertionException $e) {
            throw new InvalidPostDataException($e->getMessage());
        }
    }

    protected function validate(Post $post)
    {
        lazy()
            ->that($post->getTitle())->notBlank()->minLength(3)
            ->that($post->getContent())->notBlank()->minLength(10)
            ->that($post->getPublishedAt())->nullOr()->isInstanceOf(DateTimeInterface::class)
            ->verifyNow();
    }
}
