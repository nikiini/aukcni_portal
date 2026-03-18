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
    //  Inicializuje závislosti potřebné pro fungování třídy.
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Aukce::class);
    }
    //  Spočítá aktivní aukce vybraného uživatele.
    public function pocetAktivnichAukciUzivatele(int $uzivatelId): int{
        $aktualniCas = new \DateTime();
        return (int) $this->createQueryBuilder('aktivniAukce')
            ->select('COUNT(aktivniAukce.id)')
            ->where('aktivniAukce.uzivatel = :uzivatel')
            ->andWhere('aktivniAukce.stav =:stav')
            ->andWhere('aktivniAukce.cas_konce > :aktualniCas')
            ->andWhere('aktivniAukce.skryta = false')
            ->setParameter('uzivatel', $uzivatelId)
            ->setParameter('stav', 'aktivni')
            ->setParameter('aktualniCas', $aktualniCas)
            ->getQuery()
            ->getSingleScalarResult();
    }
    //  Spočítá ukončené aukce vybraného uživatele.
    public function pocetUkoncenychAukciUzivatele(int $uzivatelId): int
    {
        $aktualniCas = new \DateTime();
        return (int) $this->createQueryBuilder('ukoncenaAukce')
            ->select('COUNT(ukoncenaAukce.id)')
            ->where('ukoncenaAukce.uzivatel = :uzivatel')
            ->andWhere('ukoncenaAukce.cas_konce <= :aktualniCas')
            ->andWhere('ukoncenaAukce.skryta = false')
            ->setParameter('uzivatel', $uzivatelId)
            ->setParameter('aktualniCas', $aktualniCas)
            ->getQuery()
            ->getSingleScalarResult();
    }
    //  Vrátí doporučené aukce pro domovskou stránku.
    public function najdiDoporucene(int $pocet = 6, ?int $typId = null): array
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.aukceKategorie', 'ak')
            ->leftJoin('ak.kategorie', 'k')
            ->where('a.stav = :stav')
            ->andWhere('a.skryta = false')
            ->setParameter('stav', 'aktivni');

        if ($typId) {
            $qb->andWhere('k.id = :typId')
                ->setParameter('typId', $typId);
        }

        return $qb
            ->orderBy('a.cas_zacatku', 'DESC')   // zatím bereme nejnovější jako doporučené
            ->setMaxResults($pocet)
            ->getQuery()
            ->getResult();
    }
    // Vyhledá aukce vytvořené během dneška.
    public function najdiNoveAukceDnes(int $pocet = 6, ?int $typId = null): array
    {
        $zacatekDne = new \DateTime('today');

        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.aukceKategorie', 'ak')
            ->leftJoin('ak.kategorie', 'k')
            ->where('a.stav = :stav')
            ->andWhere('a.cas_zacatku >= :zacatekDne')
            ->andWhere('a.skryta = false')
            ->setParameter('stav', 'aktivni')
            ->setParameter('zacatekDne', $zacatekDne);

        if ($typId) {
            $qb->andWhere('k.id = :typId')
                ->setParameter('typId', $typId);
        }

        return $qb
            ->orderBy('a.cas_zacatku', 'DESC')
            ->setMaxResults($pocet)
            ->getQuery()
            ->getResult();
    }
    //  Vrátí aukce, kterým se blíží konec.
    public function najdiAukceKonciBrzy(int $pocet = 6, ?int $typId = null): array
    {
        $aktualniCas = new \DateTime();

        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.aukceKategorie', 'ak')
            ->leftJoin('ak.kategorie', 'k')
            ->where('a.stav = :stav')
            ->andWhere('a.cas_konce > :aktualniCas')
            ->andWhere('a.skryta = false')
            ->setParameter('stav', 'aktivni')
            ->setParameter('aktualniCas', $aktualniCas);

        if ($typId) {
            $qb->andWhere('k.id = :typId')
                ->setParameter('typId', $typId);
        }

        return $qb
            ->orderBy('a.cas_konce', 'ASC')
            ->setMaxResults($pocet)
            ->getQuery()
            ->getResult();
    }
    //  Vyhledá aktivní aukce podle textu a volitelných filtrů.
    public function vyhledatAktivni(?string $dotaz, ?int $typId = null): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('DISTINCT a')
            ->leftJoin('a.aukceKategorie', 'ak')
            ->leftJoin('ak.kategorie', 'k')
            ->addSelect('ak', 'k')
            ->where('a.stav = :stav')
            ->andWhere('a.skryta = false')
            ->setParameter('stav', 'aktivni');

        if ($dotaz) {
            $qb->andWhere('a.nazev LIKE :q OR a.popis LIKE :q')
                ->setParameter('q', '%' . $dotaz . '%');
        }

        if($typId) {
            $qb->andWhere('k.id = :typId')
                ->setParameter('typId', $typId);
        }

        return $qb
            ->orderBy('a.cas_zacatku', 'DESC')
            ->getQuery()
            ->getResult();
    }
    //  Vrátí aktivní aukce přihlášeného uživatele filtrované podle kategorie.
    public function najdiMojeAktivniPodleKategorie(int $uzivatelId, ?int $typId): array {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.aukceKategorie', 'ak')
            ->leftJoin('ak.kategorie', 'k')
            ->andWhere('a.uzivatel = :uzivatel')
            ->andWhere('a.skryta = false')
            ->setParameter('uzivatel', $uzivatelId);

        if ($typId) {
            $qb->andWhere('k.id = :typId')
                ->setParameter('typId', $typId);
        }

        return $qb
            ->orderBy('a.cas_konce', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
