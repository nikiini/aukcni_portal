<?php

namespace App\Repository;

use App\Entity\Kategorie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Kategorie>
 */
class KategorieRepository extends ServiceEntityRepository
{
    //  Inicializuje závislosti potřebné pro fungování třídy.
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Kategorie::class);
    }
    //  Vrátí všechny kategorie seřazené podle názvu.
    public function najdiVseAbecedne(): array{
        return $this->createQueryBuilder('k')
            ->orderBy('k.nazev', 'ASC')
            ->getQuery()
            ->getResult();
    }
    //  Vrátí kategorie používané v aukcích konkrétního uživatele.
    public function najdiProUzivateleAukce(int $uzivatelId): array {
        return $this->createQueryBuilder('k')
            ->innerJoin('k.aukceKategorie', 'ak')
            ->innerJoin('ak.aukce', 'a')
            ->andWhere('a.uzivatel = :uzivatel')
            ->setParameter('uzivatel', $uzivatelId)
            ->groupBy('k.id')
            ->orderBy('k.nazev', 'ASC')
            ->getQuery()
            ->getResult();
    }
    //    /**
    //     * @return Kategorie[] Returns an array of Kategorie objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('k')
    //            ->andWhere('k.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('k.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Kategorie
    //    {
    //        return $this->createQueryBuilder('k')
    //            ->andWhere('k.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
