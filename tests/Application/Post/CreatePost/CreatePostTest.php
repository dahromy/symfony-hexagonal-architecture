<?php

namespace App\Tests\Application\Post\CreatePost;

use App\Application\Post\CreatePost\CreatePostCommand;
use App\Application\Post\CreatePost\CreatePostUseCase;

use App\Domain\Post\Post;

use App\Infrastructure\Persistence\Doctrine\Post\Post as PostEntity;

use App\Infrastructure\Persistence\Doctrine\Post\PostDoctrineRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CreatePostTest extends KernelTestCase
{
    public function testCreatePost()
    {
        // To use InMemoryPostRepository uncomment the below line
        // $postRepository = new InMemoryPostRepository();

        // To use PostDoctrineRepository uncomment the below line
        /** @var PostDoctrineRepository $postRepository */
        $postRepository = static::getContainer()->get('doctrine')->getRepository(PostEntity::class);

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
