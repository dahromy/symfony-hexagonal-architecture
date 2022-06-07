<?php

namespace App\Infrastructure\Persistence\Doctrine\Post;

use App\Domain\Post\Services\PostRepositoryInterface;
use App\Domain\Post\Post as PostDomain;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostDoctrineRepository extends ServiceEntityRepository implements PostRepositoryInterface
{
    private PostDoctrineParser $doctrineParser;

    /**
     * @param ManagerRegistry $registry
     * @param PostDoctrineParser $doctrineParser
     */
    public function __construct(ManagerRegistry $registry, PostDoctrineParser $doctrineParser)
    {
        parent::__construct($registry, Post::class);
        $this->doctrineParser = $doctrineParser;
    }

    public function add(Post $entity, bool $flush = false): Post
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }

        return $entity;
    }

    public function remove(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function save(PostDomain $post) : PostDomain
    {
        $postDoctrine = $this->doctrineParser->toDoctrine($post);
        $post = $this->add($postDoctrine, true);

        return $this->doctrineParser->toDomain($post);
    }

    public function findOneByUuid(string $uuid): ?PostDomain
    {
        $postDoctrine = $this->find(Uuid::fromString($uuid));
        return is_null($postDoctrine) ? null : $this->doctrineParser->toDomain($postDoctrine);
    }
}
