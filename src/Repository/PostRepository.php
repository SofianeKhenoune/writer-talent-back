<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
     * Get one random Post (QB)
     */
    public function findOneRandomPost()
    {
        $query = $this->createQueryBuilder('p')
        ->orderBy('RAND()')
        ->setMaxResults(1);


        return $query->getQuery()->getResult();
    }

    /**
     * Get all posts publicated from a given user
     */
    public function findPublicatedPostFromUser($user)
    {
        $query = $this->createQueryBuilder('p')
        ->where('p.user = :user')
        ->andWhere('p.status = 2')
        ->setParameter('user', $user);

        return $query->getQuery()->getResult();
    }

    /**
     * Get all posts awaiting of publication from a given user
     */
    public function findAwaitingPostFromUser($user)
    {
        $query = $this->createQueryBuilder('p')
        ->where('p.user = :user')
        ->andWhere('p.status = 1')
        ->setParameter('user', $user);

        return $query->getQuery()->getResult();
    }

    /**
     * Get all posts saved from a given user
     */
    public function findSavedPostFromUser($user)
    {
        $query = $this->createQueryBuilder('p')
        ->where('p.user = :user')
        ->andWhere('p.status = 0')
        ->setParameter('user', $user);

        return $query->getQuery()->getResult();
    }

    /**
     * Get all posts publicated
     */
    public function findAllPublicated()
    {
        $query = $this->createQueryBuilder('p')
        ->Where('p.status = 2');

        return $query->getQuery()->getResult();
    }

    /**
     * Get all posts publicated frm a given genre
     */
    public function findAllPublicatedByGenre($genre)
    {
        $query = $this->createQueryBuilder('p')
        ->Where('p.status = 2')
        ->andWhere('p.genre = :genre')
        ->setParameter('genre', $genre);

        return $query->getQuery()->getResult();
    }

    /**
     * Get all posts publicated frm a given genre
     */
    public function findAllPublicatedByCategory($category)
    {
        $query = $this->createQueryBuilder('p')
        ->Where('p.status = 2')
        ->andWhere('p.category = :category')
        ->setParameter('category', $category);

        return $query->getQuery()->getResult();
    }

//    /**
//     * @return Post[] Returns an array of Post objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Post
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
