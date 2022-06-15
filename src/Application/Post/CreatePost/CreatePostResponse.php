<?php

namespace App\Application\Post\CreatePost;

use App\Domain\Post\Post;

class CreatePostResponse
{
    private Post $post;

    /**
     * @param Post $post
     */
    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    /**
     * @return Post
     */
    public function getPost(): Post
    {
        return $this->post;
    }
}
