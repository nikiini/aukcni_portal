<?php

namespace App\Controller;
use App\Entity\Aukce;
use App\Entity\Uzivatel;
use App\Entity\Sazky;
use App\Entity\Komentare;
use App\Entity\Notifikace;
use App\Repository\KomentareRepository;
use App\Repository\SazkyRepository;
use App\Form\AukceType;
use App\Repository\AukceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AukceController extends AbstractController
{
    #[Route('/aukce', name: 'aukce')]
    public function index(AukceRepository $aukceRepository): Response
    {
        $aukceVsechny = $aukceRepository->findAll();
        $aukceDoporucene = $aukceRepository->najdiNoveAukce(12);
        $aukceNoveDnes = $aukceRepository->najdiNoveAukceDnes(12);
        $aukceKonciBrzy = $aukceRepository->najdiAukceKonciBrzy(12);

        $uzivatel = $this->getUser();
        $pocetAktivnich = 0;
        if ($uzivatel) {
            $pocetAktivnich = $aukceRepository->pocetAktivnichAukciUzivatele($uzivatel->getId());
        }
        return $this->render('aukce/index.html.twig', [
            'aukce' => $aukceVsechny,
            'aukceDoporucene' => $aukceDoporucene,
            'aukceNoveDnes' => $aukceNoveDnes,
            'aukceKonciBrzy' => $aukceKonciBrzy,
            'pocetAktivnich' => $pocetAktivnich,
        ]);
    }

    #[Route('/aukce/moje', name: 'aukce_moje')]
    public function moje(AukceRepository $aukceRepository): Response
    {
        $uzivatel = $this->getUser();

        if (!$uzivatel) {
            return $this->redirectToRoute('app_login');
        }

        $aukce = $aukceRepository->findBy([
            'uzivatel' => $uzivatel
        ]);

        return $this->render('aukce/index.html.twig', [
            'aukce' => $aukce,
            'pocetAktivnich' => count($aukce),
        ]);
    }

    #[Route('/aukce/nova', name: 'aukce_nova')]
    public function nova(Request $request, EntityManagerInterface $entityManager): Response
    {
        $aukce = new Aukce();
        $uzivatel = $this->getUser();
        if (!$uzivatel) {
            return $this->redirectToRoute('app_login');
        }
        $uzivatel = $entityManager->getRepository(Uzivatel::class)->find($uzivatel->getId());
        $pocetAktivnich = $entityManager->getRepository(Aukce::class)->pocetAktivnichAukciUzivatele($uzivatel->getId());
        if ($pocetAktivnich >= 5) {
            $this->addFlash(
                'error',
                'Máte již maximální počet 5 aktivních aukcí.'
            );
            return $this->redirectToRoute('aukce');
        }
        $aukce->setUzivatel($uzivatel);

        $formular = $this->createForm(AukceType::class, $aukce);
        $formular->handleRequest($request);
        if ($formular->isSubmitted() && $formular->isValid()) {
            $delka = (int)$formular->get('delkaCasu')->getData();
            $casZacatku = new \Datetime();
            $aukce->setCasZacatku($casZacatku);
            $aukce->setCasKonce((clone $casZacatku)->modify('+' . $delka . ' days'));
            $aukce->setStav('aktivni');
            $aukce->setAktualniCena($aukce->getVychoziCena());
            $entityManager->persist($aukce);
            $entityManager->flush();
            $this->addFlash('success', 'Aukce byla úspěšně vytvořena.');
            return $this->redirectToRoute('aukce');
        }
        return $this->render('aukce/nova.html.twig', [
            'formular' => $formular->createView(),
        ]);
    }

    #[Route('/aukce/{id}', name: 'aukce_detail')]
    public function detail(Aukce $aukce, SazkyRepository $sazkyRepository, KomentareRepository $komentareRepository, EntityManagerInterface $entityManager): Response
    {
        $ted = new \DateTime();
        if($aukce->getStav() === 'aktivni' && $aukce->getCasKonce() <= $ted) {
            $aukce->setStav('ukoncena');

            $nejvyssiSazka = $sazkyRepository->findOneBy([
                'aukce' => $aukce],
                ['castka' => 'DESC', 'vytvoreno' => 'ASC'
            ]);

            if($nejvyssiSazka) {
                $vitez = $nejvyssiSazka->getUzivatel();
                $aukce->setVitez($vitez);

                $notifikaceVyhra = new Notifikace();
                $notifikaceVyhra->setUzivatel($vitez);
                $notifikaceVyhra->setTyp('vyhra');
                $notifikaceVyhra->setStav('nova');
                $notifikaceVyhra->setText('Vyhrál(a) jste aukci "' . $aukce->getNazev() . '".');
                $notifikaceVyhra->setVytvoreno(new \DateTime());
                $entityManager->persist($notifikaceVyhra);
            }

            $sazky = $sazkyRepository->findBy([
                'aukce' => $aukce],
                ['castka' => 'DESC', 'vytvoreno' => 'ASC'
            ]);
            if(!empty($sazky)) {
                $vitez = $sazky[0]->getUzivatel();
                $aukce->setVitez($vitez);
                $notifikace = new Notifikace();
                $notifikace->setUzivatel($vitez);
                $notifikace->setStav('nova');
                $notifikace->setTyp('vyhra');
                $notifikace->setText('Vyhrál(a) jste aukci "' . $aukce->getNazev() . '".');
                $notifikace->setVytvoreno(new \DateTime());
                $entityManager->persist($notifikace);
            }
            $autor = $aukce->getUzivatel();
            if($autor){
                $notifikaceAutor = new Notifikace();
                $notifikaceAutor->setUzivatel($autor);
                $notifikaceAutor->setStav('nova');
                $notifikaceAutor->setTyp('ukonceni');
                $notifikaceAutor->setText('Vaše aukce "' . $aukce->getNazev() . '" byla ukončena.');
                $notifikaceAutor->setVytvoreno(new \DateTime());
                $entityManager->persist($notifikaceAutor);
            }
            $entityManager->flush();
        }
        $sazky = $sazkyRepository->findBy(
            ['aukce' => $aukce],
            ['vytvoreno' => 'DESC']
        );
        $komentare = $komentareRepository->findBy(
            ['aukce' => $aukce],
            ['vytvoreno' => 'DESC']
        );
        return $this->render('aukce/detail.html.twig', [
            'aukce' => $aukce,
            'sazky' => $sazky,
            'komentare' => $komentare,
        ]);
    }

    #[Route('/aukce/{id}/prihodit', name: 'aukce_prihodit', methods: ['POST'])]
    public function prihodit(Aukce $aukce, Request $request, EntityManagerInterface $entityManager): Response
    {
        $uzivatelAuth = $this->getUser();
        if (!$uzivatelAuth) {
            return $this->redirectToRoute('app_login');
        }

// důležité: načti "managed" entitu uživatele z DB
        $uzivatel = $entityManager->getRepository(Uzivatel::class)->find($uzivatelAuth->getId());
        if (!$uzivatel) {
            $this->addFlash('error', 'Uživatel nebyl nalezen.');
            return $this->redirectToRoute('aukce_detail', ['id' => $aukce->getId()]);
        }


        //aukce musí být aktivní, nesmí být po konci!
        $ted = new \DateTime();
        if ($aukce->getStav() !== 'aktivni' || $aukce->getCasKonce() <= $ted) {
            $this->addFlash('error', 'Do této aukce již nelze přihazovat.');
            return $this->redirectToRoute('aukce_detail', ['id' => $aukce->getId()]);
        }

        //načtení částky z formuláře
        $castkaRaw = (string)$request->request->get('castka', '');
        $castkaRaw = str_replace(',', '.', trim($castkaRaw));

        if ($castkaRaw === '' || !is_numeric($castkaRaw)) {
            $this->addFlash('error', 'Zadejte platnou částku.');
            return $this->redirectToRoute('aukce_detail', ['id' => $aukce->getId()]);
        }
        $castka = (float)$castkaRaw;
        $aktualni = (float)($aukce->getAktualniCena() ?? $aukce->getVychoziCena());
        if ($castka <= $aktualni) {
            $this->addFlash('error', 'Částka musí být vyšší než aktuální cena.');
            return $this->redirectToRoute('aukce_detail', ['id' => $aukce->getId()]);
        }

        $sazka = new Sazky();
        $sazka->setUzivatel($uzivatel);
        $sazka->setAukce($aukce);
        $sazka->setCastka((string)$castkaRaw);
        $sazka->setVytvoreno(new \DateTime());

        $aukce->setAktualniCena((string)$castkaRaw);

        $entityManager->persist($sazka);

        $autorAukce = $aukce->getUzivatel();
        if ($autorAukce && $autorAukce->getId() !== $aukce->getId()) {
            $notifikace = new Notifikace();
            $notifikace->setUzivatel($autorAukce);
            $notifikace->setTyp('prihod');
            $notifikace->setStav('nova');
            $notifikace->setText('Do vaší aukce "' . $aukce->getNazev() . '" bylo přihozeno: ' . $castkaRaw . ' Kč.');
            $notifikace->setVytvoreno(new \DateTime());
            $entityManager->persist($notifikace);
        }
        $entityManager->flush();
        $this->addFlash('success', 'Přihození proběhlo úspěšně.');
        return $this->redirectToRoute('aukce_detail', ['id' => $aukce->getId()]);
    }


    #[Route('/aukce/{id}/komentar-pridat', name: 'aukce_komentar_pridat', methods: ['POST'])]
    public function komentarPridat(Aukce $aukce, Request $request, EntityManagerInterface $entityManager): Response
    {
        $uzivatelAuth = $this->getUser();
        if (!$uzivatelAuth) {
            return $this->redirectToRoute('app_login');
        }
        $ted = new \DateTime();
        if ($aukce->getStav() === 'ukoncena' || $aukce->getCasKonce() <= $ted) {
            $this->addFlash('error', 'Tato aukce je ukončena, komentáře již nelze přidávat.');
            return $this->redirectToRoute('aukce_detail', ['id' => $aukce->getId()]);
        }
        $uzivatel = $entityManager->getRepository(Uzivatel::class)->find($uzivatelAuth->getId());
        if (!$uzivatel) {
            $this->addFlash('error', 'Uživatel nebyl nalezen.');
            return $this->redirectToRoute('aukce_detail', ['id' => $aukce->getId()]);
        }
        $textKomentare = trim((string)$request->request->get('text_komentare', ''));
        $hodnoceniRaw = $request->request->get('hodnoceni');

        if ($textKomentare === '') {
            $this->addFlash('error', 'Komentář nesmí být prázdný.');
            return $this->redirectToRoute('aukce_detail', ['id' => $aukce->getId()]);
        }
        if (mb_strlen($textKomentare) > 500) {
            $this->addFlash('error', 'Komentář je příliš dlouhý (max. 500 znaků).');
            return $this->redirectToRoute('aukce_detail', ['id' => $aukce->getId()]);
        }
        $hodnoceni = null;
        if ($hodnoceniRaw !== null && $hodnoceniRaw !== '') {
            $hodnoceni = (int)$hodnoceniRaw;
            if ($hodnoceni < 1 || $hodnoceni > 5) {
                $this->addFlash('error', 'Hodnocení musí být v rozmezí 1 až 5.');
                return $this->redirectToRoute('aukce_detail', ['id' => $aukce->getId()]);
            }
        }
        $komentar = new Komentare();
        $komentar->setAukce($aukce);
        $komentar->setUzivatel($uzivatel);
        $komentar->setText($textKomentare);
        $komentar->setHodnoceni($hodnoceni);
        $komentar->setVytvoreno(new \DateTime());

        $entityManager->persist($komentar);

        $autorAukce = $aukce->getUzivatel();

        if ($autorAukce && $autorAukce->getId() !== $uzivatel->getId()) {
            $notifikace = new Notifikace();
            $notifikace->setUzivatel($autorAukce);
            $notifikace->setTyp('komentar');
            $notifikace->setStav('nova');
            $notifikace->setText('K vaší aukci "' . $aukce->getNazev() . '"byl přidán nový komentář.');
            $notifikace->setVytvoreno(new \DateTime());
            $entityManager->persist($notifikace);
        }
        $entityManager->flush();

        $this->addFlash('success', 'Komentář byl přidán.');
        return $this->redirectToRoute('aukce_detail', ['id' => $aukce->getId()]);
    }
}
