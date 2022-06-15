<?php

namespace App\Tests\Application\Post\CreatePost;

use App\Application\Post\CreatePost\CreatePostCommand;
use App\Application\Post\CreatePost\CreatePostResponse;
use App\Application\Post\CreatePost\CreatePostUseCase;
use App\Domain\Post\Exceptions\InvalidPostDataException;
use App\Domain\Shared\IdGenerator;
use App\Infrastructure\Persistence\Doctrine\Post\Post as PostEntity;
use App\Infrastructure\Persistence\Doctrine\Post\PostDoctrineRepository;
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
        $idGenerator = new IdGenerator;
        $postRepository = $this->getRepository('doctrine');

        $createPostUserCase = new CreatePostUseCase($postRepository, $idGenerator);

        $createPostCommand = new CreatePostCommand(
            'Post title for test',
            'Post description form test',
            new DateTime('2022-06-06 22:50:00')
        );

        $postResponse = $createPostUserCase->create($createPostCommand);

        $this->assertInstanceOf(CreatePostResponse::class, $postResponse);
        $this->assertEquals($postResponse->getPost(), $postRepository->findOneById($postResponse->getPost()->getId()));
    }

    /**
     * @param string $type
     *
     * @return InFilePostRepository|InMemoryPostRepository|PostDoctrineRepository
     */
    public function getRepository(string $type = 'memory')
    {
        switch ($type) {
            case'file';
                $fileSystem = new Filesystem();
                $fileHandler = new FilesystemHandler($fileSystem, static::getContainer()->getParameter('app.db_in_files.root_folder'));
                $postParser = new InFilePostParser();
                $repository = new InFilePostRepository($fileHandler, $postParser);

                break;
            case'doctrine':
                $repository = static::getContainer()->get('doctrine')->getRepository(PostEntity::class);
                break;
            default:
                $repository = new InMemoryPostRepository();
                break;
        }

        return $repository;
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

        $idGenerator = new IdGenerator;
        $postRepository = $this->getRepository();

        $createPostUserCase = new CreatePostUseCase($postRepository, $idGenerator);

        $createPostCommand = new CreatePostCommand(
            $postData['title'] ?? '',
            $postData['content'] ?? '',
            $postData['publishedAt'] ?? NULL
        );

        $createPostUserCase->create($createPostCommand);
    }

    /**
     * @return array
     */
    public function provideTrimInvalidData(): array
    {
        return [
            [['title' => 'Mon titre', 'publishedAt' => new DateTime('2022-05-06 12:00:05')]],
            [['publishedAt' => new DateTime('2022-05-06 12:00:05')]],
            [[]],
        ];
    }
}
