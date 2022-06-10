<?php

namespace App\Tests\Application\Post\CreatePost;

use App\Domain\Post\Exceptions\InvalidPostDataException;
use App\Domain\Post\Post;

use App\Application\Post\CreatePost\CreatePostCommand;
use App\Application\Post\CreatePost\CreatePostUseCase;

use App\Infrastructure\Persistence\Doctrine\Post\PostDoctrineRepository;
use App\Infrastructure\Persistence\Doctrine\Post\Post as PostEntity;
use App\Infrastructure\Persistence\InFile\FilesystemHandler;
use App\Infrastructure\Persistence\InFile\Post\InFilePostParser;
use App\Infrastructure\Persistence\InFile\Post\InFilePostRepository;
use App\Infrastructure\Persistence\InMemory\Post\InMemoryPostRepository;

use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;

class CreatePostTest extends KernelTestCase
{
    /**
     * @throws Exception
     */
    public function testCreatePost()
    {
        // To use InMemoryPostRepository uncomment the below line

        $postRepository = new InMemoryPostRepository();

        // To use PostDoctrineRepository uncomment the below line

        /** @var PostDoctrineRepository $postRepository */
        // $postRepository = static::getContainer()->get('doctrine')->getRepository(PostEntity::class);

        // To use InFilePostRepository uncomment the below line

        // $fileSystem = new Filesystem();
        // $fileHandler = new FilesystemHandler($fileSystem, static::getContainer()->getParameter('app.db_in_files.root_folder'));
        // $postParser = new InFilePostParser();
        // $postRepository = new InFilePostRepository($fileHandler, $postParser);

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

    /**
     * @param $postData
     *
     * @return void
     * @dataProvider provideTrimInvalidData
     * @throws InvalidPostDataException
     *
     */
    public function testCreatePostInvalidData($postData)
    {
        $this->expectException(InvalidPostDataException::class);

        // To use InMemoryPostRepository uncomment the below line

        $postRepository = new InMemoryPostRepository();

        // To use PostDoctrineRepository uncomment the below line

        /** @var PostDoctrineRepository $postRepository */
        // $postRepository = static::getContainer()->get('doctrine')->getRepository(PostEntity::class);

        // To use InFilePostRepository uncomment the below line

        // $fileSystem = new Filesystem();
        // $fileHandler = new FilesystemHandler($fileSystem, static::getContainer()->getParameter('app.db_in_files.root_folder'));
        // $postParser = new InFilePostParser();
        // $postRepository = new InFilePostRepository($fileHandler, $postParser);

        $createPostUserCase = new CreatePostUseCase($postRepository);

        $createPostCommand = new CreatePostCommand(
            $postData['title'] ?? '',
            $postData['content'] ?? '',
            $postData['publishedAt'] ?? null
        );

        $createPostUserCase->execute($createPostCommand);
    }

    public function provideTrimInvalidData(): array
    {
        return [
            [['title' => 'Mon titre', 'publishedAt' => new DateTime('2022-05-06 12:00:05')]],
            [['publishedAt' => new DateTime('2022-05-06 12:00:05')]],
            [[]],
        ];
    }
}
