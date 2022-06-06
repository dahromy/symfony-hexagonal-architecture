<?php

namespace App\Application\Blog\CreatePost;

use App\Domain\Blog\Post;
use App\Domain\Blog\Services\PostRepositoryInterface;

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

        $this->postRepository->save($post);

        return $post;
    }
}
