<?php

namespace App\Repository;

use App\Entity\ReportAukce;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReportAukce>
 */
class ReportAukceRepository extends ServiceEntityRepository
{
    //  Inicializuje závislosti potřebné pro fungování třídy.
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReportAukce::class);
    }

    //    /**
    //     * @return ReportAukce[] Returns an array of ReportAukce objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ReportAukce
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
