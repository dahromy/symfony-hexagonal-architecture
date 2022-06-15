<?php

namespace App\Infrastructure\Persistence\Doctrine\Post;

use App\Domain\Post\Post as PostDomain;
use App\Domain\Post\Services\PostRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null find($id, $lockMode = NULL, $lockVersion = NULL)
 * @method Post|null findOneBy(array $criteria, array $orderBy = NULL)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = NULL, $limit = NULL, $offset = NULL)
 */
class PostDoctrineRepository extends ServiceEntityRepository implements PostRepositoryInterface
{
    private PostDoctrineParser $doctrineParser;

    /**
     * @param ManagerRegistry    $registry
     * @param PostDoctrineParser $doctrineParser
     */
    public function __construct(ManagerRegistry $registry, PostDoctrineParser $doctrineParser)
    {
        parent::__construct($registry, Post::class);
        $this->doctrineParser = $doctrineParser;
    }

    public function remove(Post $entity, bool $flush = FALSE): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function save(PostDomain $post): void
    {
        $postDoctrine = $this->doctrineParser->toDoctrine($post);
        $this->add($postDoctrine, TRUE);
    }

    public function add(Post $entity, bool $flush = FALSE): Post
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }

        return $entity;
    }

    public function findOneByUuid(string $uuid): ?PostDomain
    {
        $postDoctrine = $this->find(Uuid::fromString($uuid));
        return is_null($postDoctrine) ? NULL : $this->doctrineParser->toDomain($postDoctrine);
    }
}
