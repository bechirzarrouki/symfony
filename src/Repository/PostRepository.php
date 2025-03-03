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

    public function save(Post $post): void
    {
        $this->_em->persist($post);
        $this->_em->flush();
    }

    public function remove(Post $post): void
    {
        $this->_em->remove($post);
        $this->_em->flush();
    }

    // Example of custom query to fetch posts by a specific author
    public function findPostsByAuthor($authorId): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.author = :author')
            ->setParameter('author', $authorId)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // Get posts by user ID
    public function findByUserId(int $userId): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.author = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
