<?php

namespace App\Repository;

use App\Entity\Certif;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Certif>
 *
 * @method Certif|null find($id, $lockMode = null, $lockVersion = null)
 * @method Certif|null findOneBy(array $criteria, array $orderBy = null)
 * @method Certif[]    findAll()
 * @method Certif[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CertifRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Certif::class);
    }

//    /**
//     * @return Certif[] Returns an array of Certif objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Certif
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
