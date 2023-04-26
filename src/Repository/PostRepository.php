<?php

namespace App\Repository;

use DateInterval;
use App\Entity\Post;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;


/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function add(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Get one random Post
     */
    public function findOneRandomPost()
    {
        $query = $this->createQueryBuilder('p')
        ->where('p.status = 2')
        ->orderBy('RAND()')
        ->setMaxResults(1);

        return $query->getQuery()->getResult();
    }

    /**
     * Get all posts publicated of a given category
     * ManyToMany relation prevent using findBy, need to make a custom request with juncture
     * @param \App\Entity\Category $category
     */
    public function findAllPublicatedByCategory($category)
    {
        $query = $this->createQueryBuilder('p')
        ->Where('p.status = 2')
        ->join('p.categories', 'c', 'WITH', 'c.id = :id')
        ->setParameter('id', $category);

        return $query->getQuery()->getResult();
    }

    /**
     * Get posts with sorts given
     * method used in backoffice controller
     * @param string $tri
     * @param int $status
     * @deprecated use findBy instead
     */
    public function findWithSort(?string $tri, int $status= null)
    {
        $query = $this->createQueryBuilder('p')
        ->orderBy('p.'.$tri, 'ASC');

            if ($status !== null) {
                $query->where('p.status = :status')
                ->setParameter('status', $status);
            }
 
        return $query->getQuery()->getResult();
    }
}
