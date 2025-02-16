<?php

namespace App\Repository;

use App\Entity\ReturnEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReturnEntityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReturnEntity::class);
    }

    // Example: Custom method to find ReturnEntity by Status
    public function findByStatus(string $status)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.status = :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getResult();
    }

    // Example: Custom method to find ReturnEntity by Deadline
    public function findByDeadline(\DateTimeInterface $deadline)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.dateDeadline <= :deadline')
            ->setParameter('deadline', $deadline)
            ->getQuery()
            ->getResult();
    }

    // Other custom methods based on your needs can go here
}
