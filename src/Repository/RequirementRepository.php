<?php

namespace App\Repository;

use App\Entity\Requirement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Requirement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Requirement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Requirement[]    findAll()
 * @method Requirement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RequirementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Requirement::class);
    }

    // Example custom query to find Requirements by Project ID
    public function findByProjectId($projectId)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.project = :projectId')
            ->setParameter('projectId', $projectId)
            ->orderBy('r.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // Example custom query to find Requirements by Status
    public function findByStatus($status)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.status = :status')
            ->setParameter('status', $status)
            ->orderBy('r.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
