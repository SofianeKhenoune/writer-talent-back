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
     * Get all posts awaiting for validation
     */
    public function findAwaitingPosts()
    {
        $query = $this->createQueryBuilder('p')
        ->where('p.status = 1');

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
     * Get all posts published
     */
    public function findAllPublicated()
    {
        $query = $this->createQueryBuilder('p')
        ->Where('p.status = 2');

        return $query->getQuery()->getResult();
    }

    /**
     * Get all post published less than a month ago
     */
    public function findMostRecent()
    {
        $now = new DateTimeImmutable();
        $thirtyDaysAgo = $now->sub(new DateInterval("P30D"));

        $qb = $this->createQueryBuilder('p');

        $qb
        ->add('where', $qb->expr()->between(
            'p.publishedAt',
            ':from',
            ':to'
            )
        )
        ->setParameters(array('from' => $thirtyDaysAgo, 'to' => $now));

 
        return $qb->getQuery()->getResult();
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
     * Get all posts publicated frm a given user
     */
    public function findAllPublicatedByUser($user)
    {
        $query = $this->createQueryBuilder('p')
        ->Where('p.status = 2')
        ->andWhere('p.user = :user')
        ->setParameter('user', $user);

        return $query->getQuery()->getResult();
    }

    /**
     * Get all posts publicated frm a given genre
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
     * Get most liked posts (limit 4)
     */
    public function findMostLiked()
    {
        $query = $this->createQueryBuilder('p')
        ->Where('p.status = 2')
        ->orderBy('p.nbLikes', 'DESC')
        ->setMaxResults(4);

        return $query->getQuery()->getResult();
    }

    public function findWithSort(?string $tri)
    {
        // Creating QueryBuilder
        $qb = $this->createQueryBuilder('p')
                ->orderBy('p.'.$tri, 'ASC');
            

        // on retourne l'exécution de la requête
        return $qb->getQuery()->getResult();
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
