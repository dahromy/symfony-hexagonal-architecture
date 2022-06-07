<?php

namespace App\Application\Post\CreatePost;

use App\Domain\Post\Post;
use App\Domain\Post\Services\PostRepositoryInterface;

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

    public function execute(CreatePostCommand $createPostCommand): Post
    {
        $post = new Post($createPostCommand->getTitle(), $createPostCommand->getContent(), $createPostCommand->getPublishedAt());

        return $this->postRepository->save($post);
    }
}
