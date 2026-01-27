<?php

namespace App\Repository;

use App\Entity\Sazky;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sazky>
 */
class SazkyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sazky::class);
    }

    //    /**
    //     * @return Sazky[] Returns an array of Sazky objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Sazky
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function soucetPrihozuUzivatele(int $uzivatelId): float{
        return (float) $this->createQueryBuilder('s')
            ->select('SUM(s.castka)')
            ->where('s.uzivatel = :uzivatel')
            ->setParameter('uzivatel', $uzivatelId)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
