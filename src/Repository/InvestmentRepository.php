<?php
namespace App\Repository;

use App\Entity\Investment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

class InvestmentRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Investment::class);
        $this->entityManager = $entityManager;
    }

    public function save(Investment $investment, bool $flush = true): void
    {
        $this->entityManager->persist($investment);
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    public function remove(Investment $investment, bool $flush = true): void
    {
        $this->entityManager->remove($investment);
        if ($flush) {
            $this->entityManager->flush();
        }
    }
    public function findByUserId(int $userId): array
    {
        return $this->createQueryBuilder('i')
            ->innerJoin('i.user', 'u') // Assuming 'author' is the relation in the Investment entity
            ->where('u.id = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }
}
