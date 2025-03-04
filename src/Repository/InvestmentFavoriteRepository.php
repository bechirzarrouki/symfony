<?php
// src/Repository/InvestmentFavoriteRepository.php

namespace App\Repository;

use App\Entity\InvestmentFavorite;
use App\Entity\User;
use App\Entity\Investment;  // Import the Investment entity
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InvestmentFavorite>
 *
 * @method InvestmentFavorite|null find($id, $lockMode = null, $lockVersion = null)
 * @method InvestmentFavorite|null findOneBy(array $criteria, array $orderBy = null)
 * @method InvestmentFavorite[]    findAll()
 * @method InvestmentFavorite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvestmentFavoriteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InvestmentFavorite::class);
    }

    /**
     * Find a favorite by user and investment.
     */
    public function findByUserAndInvestment(User $user, Investment $investment): ?InvestmentFavorite
    {
        return $this->createQueryBuilder('if')
            ->andWhere('if.user = :user')
            ->andWhere('if.investment = :investment')
            ->setParameter('user', $user)
            ->setParameter('investment', $investment)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find all favorites by a specific user.
     */
    public function findFavoritesByUser(User $user): array
    {
        return $this->createQueryBuilder('if')
            ->andWhere('if.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    /**
     * Count the number of times an investment has been favorited.
     */
    public function countFavoritesByInvestment(Investment $investment): int
    {
        return $this->createQueryBuilder('if')
            ->select('COUNT(if.id)')
            ->andWhere('if.investment = :investment')
            ->setParameter('investment', $investment)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
