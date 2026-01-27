<?php

namespace App\Repository;

use App\Entity\Aukce;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
//use mysql_xdevapi\CollectionRemove;

/**
 * @extends ServiceEntityRepository<Aukce>
 */
class AukceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Aukce::class);
    }

    //    /**
    //     * @return Aukce[] Returns an array of Aukce objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Aukce
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function pocetAktivnichAukciUzivatele(int $uzivatelId): int{
        $aktualniCas = new \DateTime();
        return (int) $this->createQueryBuilder('aktivniAukce')
            ->select('COUNT(aktivniAukce.id)')
            ->where('aktivniAukce.uzivatel = :uzivatel')
            ->andWhere('aktivniAukce.stav =:stav')
            ->andWhere('aktivniAukce.cas_konce > :aktualniCas')
            ->setParameter('uzivatel', $uzivatelId)
            ->setParameter('stav', 'aktivni')
            ->setParameter('aktualniCas', $aktualniCas)
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function pocetUkoncenychAukciUzivatele(int $uzivatelId): int
    {
        $aktualniCas = new \DateTime();
        return (int) $this->createQueryBuilder('ukoncenaAukce')
            ->select('COUNT(ukoncenaAukce.id)')
            ->where('ukoncenaAukce.uzivatel = :uzivatel')
            ->andWhere('ukoncenaAukce.cas_konce <= :aktualniCas')
            ->setParameter('uzivatel', $uzivatelId)
            ->setParameter('aktualniCas', $aktualniCas)
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function najdiNoveAukce(int $pocet = 6):array
    {
        return $this->createQueryBuilder('aukce')
            ->andWhere('aukce.stav = :stav')
            ->setParameter('stav', 'aktivni')
            ->orderBy('aukce.cas_zacatku', 'DESC')
            ->setMaxResults($pocet)
            ->getQuery()
            ->getResult();
    }
    public function najdiNoveAukceDnes(int $pocet = 6):array
    {
        $zacatekDne = new \DateTime('today');
        return $this->createQueryBuilder('aukce')
            ->andWhere('aukce.stav = :stav')
            ->andWhere('aukce.cas_zacatku >= :zacatekDne')
            ->setParameter('stav', 'aktivni')
            ->setParameter('zacatekDne', $zacatekDne)
            ->orderBy('aukce.cas_zacatku', 'DESC')
            ->setMaxResults($pocet)
            ->getQuery()
            ->getResult();
    }
    public function najdiAukceKonciBrzy(int $pocet = 6):array
    {
        $casTed = new \DateTime();
        return $this->createQueryBuilder('aukce')
            ->andWhere('aukce.stav = :stav')
            ->andwhere('aukce.cas_konce > :casTed')
            ->setParameter('stav', 'aktivni')
            ->setParameter('casTed', $casTed)
            ->orderBy('aukce.cas_konce', 'ASC')
            ->setMaxResults($pocet)
            ->getQuery()
            ->getResult();
    }
    public function najdiAktivniAukce()
    {
        $aktualniCas = new \DateTime();
        return $this->createQueryBuilder('a')
            ->where('a.stav = :stav')
            ->andWhere('a.cas_konce > :aktualniCas')
            ->setParameter('stav', 'aktivni')
            ->setParameter('aktualniCas', $aktualniCas)
            ->orderBy('a.cas_konce', 'ASC')
            ->getQuery()
            ->getResult();
    }
    public function najdiUkonceneAukce()
    {
        $aktualniCas = new \DateTime();
        return $this->createQueryBuilder('a')
            ->where('a.cas_konce <= :aktualniCas')
            ->setParameter('aktualniCas', $aktualniCas)
            ->orderBy('a.cas_konce', 'DESC')
            ->getQuery()
            ->getResult();
    }

}
