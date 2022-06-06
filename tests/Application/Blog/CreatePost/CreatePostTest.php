<?php

namespace App\Tests\Application\Blog\CreatePost;

use App\Application\Blog\CreatePost\CreatePostCommand;
use App\Application\Blog\CreatePost\CreatePostUseCase;

use App\Domain\Blog\Post;

use App\Infrastructure\Persistence\InMemory\InMemoryPostRepository;
use DateTime;
use PHPUnit\Framework\TestCase;

class CreatePostTest extends TestCase
{
    public function testCreatePost()
    {
        $postRepository = new InMemoryPostRepository();

        $createPostUserCase = new CreatePostUseCase($postRepository);

        $createPostCommand = new CreatePostCommand(
            'Post title for test',
            'Post description form test',
            new DateTime('2022-06-06 22:50:00')
        );

        $post = $createPostUserCase->execute($createPostCommand);

        $this->assertInstanceOf(Post::class, $post);
        $this->assertEquals($post, $postRepository->findOneByUuid($post->getUuid()));
    }
}
