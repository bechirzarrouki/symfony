<?php

namespace App\Repository;

use App\Entity\User; // Correct namespace for User entity
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    // Custom method to find users by username (if needed)
    public function findEntitiesByUsername(string $username): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.username LIKE :username')
            ->setParameter('username', '%' . $username . '%')
            ->getQuery()
            ->getResult();
    }
   // src/Repository/UserRepository.php

public function findByEmailLike(string $email): array
{
    return $this->createQueryBuilder('u')
        ->where('u.email LIKE :email')
        ->setParameter('email', '%' . $email . '%')
        ->getQuery()
        ->getResult();
}
}
