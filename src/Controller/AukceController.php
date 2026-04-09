<?php

namespace App\Controller;
use App\Entity\Aukce;
use App\Entity\AukceKategorie;
use App\Entity\FotkyAukci;
use App\Entity\Komentare;
use App\Entity\Notifikace;
use App\Entity\Sazky;
use App\Entity\Platby;
use App\Form\AukceType;
use App\Repository\AukceRepository;
use App\Repository\KategorieRepository;
use App\Repository\KomentareRepository;
use App\Repository\ReportAukceRepository;
use App\Repository\SazkyRepository;
use App\Repository\UzivatelRepository;
use App\Service\VyhodnoceniAukceService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
//  Řadič (Controller) zajišťující hlavní aplikační logiku aukcí.
//  Zpracovává zobrazení výpisů, vytváření a úpravu aukcí, a také uživatelské interakce, jako jsou příhozy nebo přidávání komentářů.
class AukceController extends AbstractController
{
    //  Zobrazí seznam aukcí včetně filtrování a řazení podle zadaných parametrů.
    #[Route('/aukce', name: 'aukce')]
    public function index(AukceRepository $aukceRepository, KategorieRepository $kategorieRepository, Request $request): Response
    {
        //  Zobrazí seznam všech aktivních aukcí, které jsou na portálu aktuálně dostupné.
        $kategorie = $kategorieRepository->najdiVseAbecedne();

        $vybranyTypId = $request->query->get('typ');
        $vyhledavaciDotaz = $request->query->get('q');
        $vybranaKategorie = null;

        if ($vybranyTypId) {
            $vybranaKategorie = $kategorieRepository->find($vybranyTypId);
        }

        $aukceVsechny = $aukceRepository->vyhledatAktivni(
            $vyhledavaciDotaz,
            $vybranyTypId ? (int)$vybranyTypId : null
        );

        $sekce = $request->query->get('sekce', 'vse');
        $aukceDoporucene = $aukceRepository->najdiDoporucene(6, $vybranyTypId);
        $aukceNoveDnes = $aukceRepository->najdiNoveAukceDnes(6, $vybranyTypId);
        $aukceKonciBrzy = $aukceRepository->najdiAukceKonciBrzy(6, $vybranyTypId);

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
            'kategorie' => $kategorie,
            'rezimStranky' => 'vse',
            'aktivniSekce' => $sekce,
            'vyhledavani' => $vyhledavaciDotaz,
            'vybranyTyp' => $vybranyTypId,
            'kategorieNavigace' => $kategorie,
            'vybranaKategorie' => $vybranaKategorie,
        ]);

    }
    //  Vrátí přehled aukcí přihlášeného uživatele.
    #[Route('/aukce/moje', name: 'aukce_moje')]
    public function moje(AukceRepository $aukceRepository, KategorieRepository $kategorieRepository, Request $request): Response
    {
        //  Vygeneruje přehled aukcí, které aktuálně přihlášený uživatel sám založil.
        $uzivatel = $this->getUser();

        if (!$uzivatel) {
            return $this->redirectToRoute('app_login');
        }
        $vybranyTypId = $request->query->get('typ');
        $vybranaKategorie = null;

        if ($vybranyTypId) {
            $vybranaKategorie = $kategorieRepository->find($vybranyTypId);
        }
        if ($vybranyTypId){
            $aukceMoje = $aukceRepository->najdiMojeAktivniPodleKategorie($uzivatel->getId(), (int)$vybranyTypId);
        } else{
            $aukceMoje = $aukceRepository->findBy(
                ['uzivatel' => $uzivatel],
                ['cas_konce' => 'DESC']
            );
        }

        $kategorie = $kategorieRepository->najdiProUzivateleAukce($uzivatel->getId());
        return $this->render('aukce/index.html.twig', [
            'aukce' => $aukceMoje,
            'aukceDoporucene' => [],
            'aukceNoveDnes' => [],
            'aukceKonciBrzy' => [],
            'kategorie' => $kategorie,
            'kategorieNavigace' => $kategorie,
            'pocetAktivnich' => count($aukceMoje),
            'rezimStranky' => 'moje',
            'aktivniSekce' => 'moje',
            'vyhledavani' => null,
            'vybranyTyp' => $vybranyTypId,
            'vybranaKategorie' => $vybranaKategorie,
        ]);
    }
    //  Zpracuje vytvoření nové aukce včetně nahrání obrázků a přiřazení kategorií.
    #[Route('/aukce/nova', name: 'aukce_nova')]
    public function nova(Request $request, EntityManagerInterface $entityManager): Response
    {
        //  Zpracuje odeslaný formulář pro založení nové aukce přihlášeným uživatelem.
        $aukce = new Aukce();
        $uzivatelAuth = $this->getUser();
        if (!$uzivatelAuth) {
            return $this->redirectToRoute('app_login');
        }
        $uzivatel = $entityManager->getRepository(\App\Entity\Uzivatel::class)
            ->find($uzivatelAuth->getId());

        if (!$uzivatel) {
            throw $this->createAccessDeniedException('Uživatel nebyl nalezen.');
        }
        if ($uzivatel->isBlokovan()) {
            $this->addFlash('error', 'Váš účet je zablokován. Nelze vytvářet aukce.');
            return $this->redirectToRoute('aukce');
        }
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

            $vybraneKategorie = $formular->get('kategorie')->getData();
            if (count($vybraneKategorie) === 0) {
                $this->addFlash('error', 'Musíte vybrat alespoň jednu kategorii.');
                return $this->render('aukce/nova.html.twig', [
                    'formular' => $formular->createView(),
                ]);
            }
            // limit MAX 5
            if (count($vybraneKategorie) > 5) {
                $this->addFlash('error', 'Můžete vybrat maximálně 5 kategorií.');
                return $this->render('aukce/nova.html.twig', [
                    'formular' => $formular->createView(),
                ]);
            }
            $aukce->setVerejneId(bin2hex(random_bytes(8)));
            $entityManager->persist($aukce);
            foreach ($vybraneKategorie as $kategorie) {
                $aukceKategorie = new AukceKategorie();
                $aukceKategorie->setAukce($aukce);
                $aukceKategorie->setKategorie($kategorie);
                $entityManager->persist($aukceKategorie);
            }
            $fotky = $formular->get('fotky')->getData() ?? [];

            $hlavni = true;
            foreach ($fotky as $foto) {
                if ($foto) {
                    $ext = $foto->guessExtension() ?: 'jpg';
                    $novyNazev = uniqid().'.'.$ext;
                    $foto->move(
                        $this->getParameter('kernel.project_dir').'/public/uploads',
                        $novyNazev
                    );
                    $fotka = new FotkyAukci();
                    $fotka->setAukce($aukce);
                    $fotka->setCesta($novyNazev);
                    $fotka->setVytvoreno(new \DateTime());
                    // první fotka = hlavní
                    if (!$aukce->getHlavniFoto()) {
                        $aukce->setHlavniFoto($novyNazev);
                    }
                    $entityManager->persist($fotka);
                }
            }
            $entityManager->flush();
            $this->addFlash('success', 'Aukce byla úspěšně vytvořena.');
            return $this->redirectToRoute('aukce');
        }
        return $this->render('aukce/nova.html.twig', ['formular' => $formular->createView(),]);
    }
    //  Zobrazí detail aukce, její příhozy, komentáře a dostupné akce.
    #[Route('/aukce/{verejneId}', name: 'aukce_detail')]
    public function detail(string $verejneId, AukceRepository $aukceRepository, ReportAukceRepository $reportRepository, SazkyRepository $sazkyRepository, KomentareRepository $komentareRepository, EntityManagerInterface $entityManager, VyhodnoceniAukceService $vyhodnoceniAukceService): Response
    {
        //  Načte a zobrazí podrobné informace o konkrétní aukci na základě jejího veřejného identifikátoru.
        //  Součástí pohledu jsou také formuláře pro vložení příhozu a nového komentáře.
        $aukce = $aukceRepository->findOneBy(['verejneId'=>$verejneId]);
        if (!$aukce) {
            throw $this->createNotFoundException('Aukce nebyla nalezena.');
        }
        $ted = new \DateTime();
        if ($aukce->getStav() === 'aktivni' && $aukce->getCasKonce() <= $ted) {
            $aukce->setStav('ukoncena');
            $vyhodnoceniAukceService->vyhodnotitUkoncenouAukci($aukce);
            $entityManager->flush();
        }
        $komentare = $komentareRepository->findBy(
            ['aukce' => $aukce, 'skryty' => false],
            ['vytvoreno' => 'DESC']
        );
        $sazky = $sazkyRepository->findBy(
            ['aukce' => $aukce],
            ['castka' => 'DESC', 'vytvoreno' => 'ASC']
        );
        $reportExistuje = false;

        if ($this->getUser()) {

            $reportExistuje = $reportRepository->findOneBy([
                    'aukce' => $aukce,
                    'nahlasujici' => $this->getUser()
                ]) !== null;
        }
        return $this->render('aukce/detail.html.twig', [
            'aukce' => $aukce,
            'sazky' => $sazky,
            'komentare' => $komentare,
            'reportExistuje' => $reportExistuje,
        ]);
    }
    //  Zpracuje nový příhoz a ověří, že splňuje pravidla aukce.
    #[Route('/aukce/{verejneId}/prihodit', name: 'aukce_prihodit', methods: ['POST'])]
    public function prihodit(string $verejneId, AukceRepository $aukceRepository, SazkyRepository $sazkyRepository,Request $request, EntityManagerInterface $entityManager, UzivatelRepository $uzivatelRepository): Response
    {
        //  Zpracuje požadavek přihlášeného uživatele na přihození částky do aukce.
        //  Metoda ověřuje, zda má uživatel dostatek kreditů a zda je příhoz vyšší než aktuální cena.
        $aukce = $aukceRepository->findOneBy(['verejneId' => $verejneId]);

        if (!$aukce) {
            throw $this->createNotFoundException('Aukce nebyla nalezena.');
        }
        $uzivatelAuth = $this->getUser();
        if (!$uzivatelAuth) {
            return $this->redirectToRoute('app_login');
        }
        if (!$this->isCsrfTokenValid(
            'prihod_' . $aukce->getVerejneId(),
            $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException('Neplatný CSRF token.');
        }

        $uzivatel = $uzivatelRepository->find($uzivatelAuth->getId());
        if (!$uzivatel) {
            $this->addFlash('error', 'Uživatel nebyl nalezen.');
            return $this->redirectToRoute('aukce_detail', ['verejneId' => $aukce->getVerejneId()]);
        }
        if ($uzivatel->isBlokovan()) {
            $this->addFlash('error', 'Váš účet je zablokován.');
            return $this->redirectToRoute('aukce_detail', ['verejneId' => $aukce->getVerejneId()]); // nebo jinam
        }
        //autor nesmí přihazovat do vlastní aukce
        if ($this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Administrátor nemůže přihazovat do aukce.');
            return $this->redirectToRoute('aukce_detail', [
                'verejneId' => $aukce->getVerejneId()
            ]);
        }
        if ($aukce->getUzivatel() && $aukce->getUzivatel()->getId() === $uzivatel->getId()) {
            $this->addFlash('error', 'Do vlastní aukce nemůžete přihazovat.');
            return $this->redirectToRoute('aukce_detail', ['verejneId' => $aukce->getVerejneId()]);
        }

        //aukce musí být aktivní, nesmí být po konci!
        $ted = new \DateTime();
        if ($aukce->getStav() !== 'aktivni' || $aukce->getCasKonce() <= $ted) {
            $this->addFlash('error', 'Do této aukce již nelze přihazovat.');
            return $this->redirectToRoute('aukce_detail', ['verejneId' => $aukce->getVerejneId()]);
        }

        //načtení částky z formuláře
        $castkaRaw = trim((string)$request->request->get('castka', ''));
        $castkaRaw = str_replace(',', '.', trim($castkaRaw));

        if ($castkaRaw === '' || !is_numeric($castkaRaw)) {
            $this->addFlash('error', 'Zadejte platnou částku.');
            return $this->redirectToRoute('aukce_detail', ['verejneId'=>$aukce->getVerejneId()]);
        }

        $castka = (float)$castkaRaw;

        if ($castka <= 0) {
            $this->addFlash('error','Částka musí být větší než 0.');
            return $this->redirectToRoute('aukce_detail', ['verejneId'=>$aukce->getVerejneId()]);
        }
        // aktualizujeme aukci z DB kvůli paralelním příhozům
        $entityManager->refresh($aukce);
        $aktualni = (float)($aukce->getAktualniCena() ?: $aukce->getVychoziCena());
        $minimalniPrirazka = 10.0;
        if ($castka < $aktualni + $minimalniPrirazka) {
            $this->addFlash('error', sprintf('Částka musí být alespoň o %.0f Kč vyšší než aktuální cena.', $minimalniPrirazka));
            return $this->redirectToRoute('aukce_detail', ['verejneId' => $aukce->getVerejneId()]);
        }
        $posledniSazka = $sazkyRepository->findOneBy(
            ['aukce' => $aukce],
            ['castka' => 'DESC']
        );

        if ($posledniSazka && $posledniSazka->getUzivatel()->getId() === $uzivatel->getId()) {
            $this->addFlash('error', 'Jste aktuálně nejvyšší přihazující. Můžete znovu přihodit až poté, co Vás někdo přehodí.');
            return $this->redirectToRoute('aukce_detail', ['verejneId' => $aukce->getVerejneId()]);
        }
        $kredityUzivatele = (float)$uzivatel->getKredity();
        if($kredityUzivatele < $castka) {
           $this->addFlash('error', 'Nemáte dostatek kreditů pro tuto sázku.');
           return $this->redirectToRoute('aukce_detail', ['verejneId' => $aukce->getVerejneId()]);
        }
        // vrácení kreditů předchozímu nejvyššímu přihazujícímu
        if ($posledniSazka) {

            $predchozi = $posledniSazka->getUzivatel();

            // vracíme kredity pouze pokud to není stejný uživatel
            if ($predchozi && $predchozi->getId() !== $uzivatel->getId()) {

                $predchoziKredity = (float)$predchozi->getKredity();
                $predchoziKredity += (float)$posledniSazka->getCastka();

                $predchozi->setKredity(round($predchoziKredity, 2));
            }
        }
        $posledniMojeSazka = $sazkyRepository->findOneBy(
                ['aukce' => $aukce, 'uzivatel' => $uzivatel],
                ['vytvoreno' => 'DESC']
            );

        if ($posledniMojeSazka) {
            $rozdil = time() - $posledniMojeSazka->getVytvoreno()->getTimestamp();

            if ($rozdil < 3) {
                $this->addFlash('error', 'Musíte počkat 3 sekundy mezi příhozy.');
                return $this->redirectToRoute('aukce_detail', ['verejneId' => $aukce->getVerejneId()]);
            }
        }
        $novyStav = $kredityUzivatele - $castka;
        $uzivatel->setKredity(round($novyStav, 2));
        $sazka = new Sazky();
        $sazka->setUzivatel($uzivatel);
        $sazka->setAukce($aukce);
        $sazka->setCastka((string)$castkaRaw);
        $sazka->setVytvoreno(new \DateTime());

        if ($castka <= $aktualni) {
            $this->addFlash('error', 'Někdo vás právě přehodil.');
            return $this->redirectToRoute('aukce_detail', [
                'verejneId' => $aukce->getVerejneId()
            ]);
        }

        $aukce->setAktualniCena((string)$castkaRaw);

        $entityManager->persist($sazka);
        $entityManager->persist($aukce);

        $autorAukce = $aukce->getUzivatel();
        if ($autorAukce && $autorAukce->getId() !== $uzivatel->getId()) {
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
        return $this->redirectToRoute('aukce_detail', ['verejneId' => $aukce->getVerejneId()]);
    }

    //  Přidá komentář nebo hodnocení k aktivní aukci.
    #[Route('/aukce/{verejneId}/komentar-pridat', name: 'aukce_komentar_pridat', methods: ['POST'])]
    public function komentarPridat(string $verejneId,UzivatelRepository $uzivatelRepository, AukceRepository $aukceRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        //  Uloží nový uživatelský komentář k vybrané aukci.
        $aukce = $aukceRepository->findOneBy(['verejneId' => $verejneId]);
        if (!$aukce) {
            throw $this->createNotFoundException('Aukce nebyla nalezena.');
        }

        if (!$this->isCsrfTokenValid(
            'komentar_'.$aukce->getVerejneId(),
            $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException('Neplatný CSRF token.');
        }
        $uzivatelAuth = $this->getUser();
        if (!$uzivatelAuth) {
            return $this->redirectToRoute('app_login');
        }
        $uzivatel = $uzivatelRepository->find($uzivatelAuth->getId());
        $ted = new \DateTime();
        if ($aukce->getStav() === 'ukoncena' || $aukce->getCasKonce() <= $ted) {
            $this->addFlash('error', 'Tato aukce je ukončena, komentáře již nelze přidávat.');
            return $this->redirectToRoute('aukce_detail', ['verejneId' => $aukce->getVerejneId()]);
        }

        if ($uzivatelAuth->isBlokovan()) {
            $this->addFlash('error', 'Váš účet je zablokován.');
            return $this->redirectToRoute('aukce_detail', ['verejneId' => $aukce->getVerejneId()]); // nebo jinam
        }
        $textKomentare = trim((string)$request->request->get('text_komentare', ''));
        $hodnoceniRaw = $request->request->get('hodnoceni');

        if ($textKomentare === '') {
            $this->addFlash('error', 'Komentář nesmí být prázdný.');
            return $this->redirectToRoute('aukce_detail', ['verejneId' => $aukce->getVerejneId()]);
        }
        if (mb_strlen($textKomentare) > 150) {
            $this->addFlash('error', 'Komentář je příliš dlouhý (max. 150 znaků).');
            return $this->redirectToRoute('aukce_detail', ['verejneId' => $aukce->getVerejneId()]);
        }
        $hodnoceni = null;
        if ($hodnoceniRaw !== null && $hodnoceniRaw !== '') {
            $hodnoceni = (int)$hodnoceniRaw;
            if ($hodnoceni < 1 || $hodnoceni > 5) {
                $this->addFlash('error', 'Hodnocení musí být v rozmezí 1 až 5.');
                return $this->redirectToRoute('aukce_detail', ['verejneId' => $aukce->getVerejneId()]);
            }
        }
        $komentar = new Komentare();
        $komentar->setVerejneId(bin2hex(random_bytes(8)));
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
            $notifikace->setText('K vaší aukci "' . $aukce->getNazev() . '" byl přidán nový komentář.');
            $notifikace->setVytvoreno(new \DateTime());
            $entityManager->persist($notifikace);
        }
        $entityManager->flush();

        $this->addFlash('success', 'Komentář byl přidán.');
        return $this->redirectToRoute('aukce_detail', ['verejneId' => $aukce->getVerejneId()]);
    }
    //  Skryje aukci v administraci, aniž by ji fyzicky smazal.
    #[Route('/admin/aukce/{verejneId}/skryt', name: 'admin_aukce_skryt')]
    public function adminAukceSkryt(string $verejneId, AukceRepository $aukceRepository, EntityManagerInterface $entityManager): Response
    {
        $aukce = $aukceRepository->findOneBy(['verejneId'=>$verejneId]);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $aukce->setSkryta(true);
        $entityManager->flush();

        $this->addFlash('success', 'Aukce byla skrytá administrátorem.');
        return $this->redirectToRoute('aukce_detail', [
            'verejneId' => $aukce->getVerejneId()
        ]);
    }
    //  Znovu zobrazí dříve skrytou aukci.
    #[Route('/admin/aukce/{verejneId}/odkryt', name: 'admin_aukce_odkryt')]
    public function adminAukceOdkryt(string $verejneId, AukceRepository $aukceRepository, EntityManagerInterface $entityManager): Response
    {
        $aukce = $aukceRepository->findOneBy(['verejneId'=>$verejneId]);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $aukce->setSkryta(false);
        $entityManager->flush();

        $this->addFlash('success', 'Aukce byla znovu zobrazena.');
        return $this->redirectToRoute('aukce_detail', [
            'verejneId' => $aukce->getVerejneId()
        ]);
    }
    //  Skryje nebo odstraní nevhodný komentář z administrace.
    #[Route('/admin/komentar/{verejneId}/smazat', name: 'admin_komentar_smazat', methods:['POST'])]
    public function adminKomentarSmazat(string $verejneId, KomentareRepository $komentareRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $komentar = $komentareRepository->findOneBy(['verejneId' => $verejneId]);

        if (!$komentar) {
            $this->addFlash('error', 'Komentář nebyl nalezen.');
            return $this->redirectToRoute('aukce');
        }

        if (!$this->isCsrfTokenValid(
            'smaz_komentar_'.$komentar->getVerejneId(),
            $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException('Neplatný CSRF token.');
        }

        $aukceVerejneId = $komentar->getAukce()->getVerejneId();

        $entityManager->remove($komentar);
        $entityManager->flush();

        $this->addFlash('success', 'Komentář byl smazán.');

        return $this->redirectToRoute('aukce_detail', [
            'verejneId' => $aukceVerejneId
        ]);
    }
    //  Umožní vlastníkovi upravit vlastní aukci před jejím ukončením.
    #[Route('/aukce/{verejneId}/upravit', name: 'aukce_upravit')]
    public function upravitVlastniAukci(string $verejneId, Request $request, AukceRepository $aukceRepository, EntityManagerInterface $entityManager): Response {
        $uzivatel = $this->getUser();

        if (!$uzivatel) {
            return $this->redirectToRoute('app_login');
        }
        $aukce = $aukceRepository->findOneBy([
            'verejneId' => $verejneId
        ]);
        if (!$aukce) {
            $this->addFlash('error', 'Aukce nebyla nalezena.');
            return $this->redirectToRoute('aukce_moje');
        }
        if (
            !$aukce->getUzivatel() ||
            ($aukce->getUzivatel()->getId() !== $uzivatel->getId() && !$this->isGranted('ROLE_ADMIN'))
        ) {
            throw $this->createAccessDeniedException('Tuto aukci můžete upravit pouze vy nebo admin.');
        }

        if ($aukce->getStav() !== 'aktivni' || $aukce->getCasKonce() <= new \DateTime()) {
            $this->addFlash('error', 'Ukončenou aukci již nelze upravovat.');
            return $this->redirectToRoute('aukce_detail', ['verejneId' => $aukce->getVerejneId()]);
        }
        $form = $this->createForm(AukceType::class, $aukce);

        // předvyplnění kategorií
        $vybraneKategorie = [];
        foreach ($aukce->getAukceKategorie() as $ak) {
            $vybraneKategorie[] = $ak->getKategorie();
        }

        $form->get('kategorie')->setData($vybraneKategorie);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $fotky = $form->get('fotky')->getData() ?? [];

            foreach ($fotky as $foto) {

                if ($foto) {

                    $ext = $foto->guessExtension() ?: 'jpg';
                    $novyNazev = uniqid().'.'.$ext;

                    $foto->move(
                        $this->getParameter('kernel.project_dir').'/public/uploads',
                        $novyNazev
                    );
                    if (!$aukce->getHlavniFoto()) {
                        $aukce->setHlavniFoto($novyNazev);
                    }
                    $fotka = new FotkyAukci();
                    $fotka->setAukce($aukce);
                    $fotka->setCesta($novyNazev);
                    $fotka->setVytvoreno(new \DateTime());
                    $entityManager->persist($fotka);

                }

            }

            // pokud ještě nikdo nepřihazoval, musí se aktualizovat aktuální cena
            if ($aukce->getSazky()->count() === 0) {
                $aukce->setAktualniCena($aukce->getVychoziCena());
            }
            $vybraneKategorie = $form->get('kategorie')->getData() ?? [];
            foreach ($aukce->getAukceKategorie() as $staraVazba) {
                $entityManager->remove($staraVazba);
            }

            foreach ($form->get('kategorie')->getData() as $kategorie) {
                $novaVazba = new AukceKategorie();
                $novaVazba->setAukce($aukce);
                $novaVazba->setKategorie($kategorie);
                $entityManager->persist($novaVazba);
            }
            $entityManager->flush();

            $this->addFlash('success', 'Aukce byla upravena.');
            return $this->redirectToRoute('aukce_detail', ['verejneId' => $aukce->getVerejneId()]);
        }
        return $this->render('aukce/upravit.html.twig', [
            'formular' => $form->createView(),
            'aukce' => $aukce,
        ]);
    }
    //  Ukončí aukci předčasně na žádost vlastníka.
    #[Route('/aukce/{verejneId}/zastavit', name: 'aukce_zastavit', methods: ['POST'])]
    public function zastavitAukci(string $verejneId, Request $request, AukceRepository $aukceRepository, EntityManagerInterface $entityManager, VyhodnoceniAukceService $vyhodnoceniAukceService): Response {

        $aukce = $aukceRepository->findOneBy(['verejneId' => $verejneId]);

        if (!$aukce) {
            throw $this->createNotFoundException('Aukce nebyla nalezena.');
        }

        $uzivatel = $this->getUser();

        if (!$uzivatel) {
            return $this->redirectToRoute('app_login');
        }

        if (
            $aukce->getUzivatel()->getId() !== $uzivatel->getId()
            && !$this->isGranted('ROLE_ADMIN')
        ) {
            throw $this->createAccessDeniedException('Tuto aukci může zastavit pouze autor nebo administrátor.');
        }

        if (!$this->isCsrfTokenValid(
            'zastavit_'.$aukce->getVerejneId(),
            $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException('Neplatný CSRF token.');
        }

        $aukce->setStav('ukoncena');

        $vyhodnoceniAukceService->vyhodnotitUkoncenouAukci($aukce);

        $entityManager->flush();

        $this->addFlash('success', 'Aukce byla ukončena.');

        return $this->redirectToRoute('aukce_detail', [
            'verejneId' => $aukce->getVerejneId()
        ]);
    }
    //  Znovu spustí dříve zastavenou aukci.
    #[Route('/aukce/{verejneId}/spustit', name: 'aukce_spustit', methods: ['POST'])]
    public function spustitAukci(string $verejneId, Request $request, AukceRepository $aukceRepository, EntityManagerInterface $entityManager): Response {
        $uzivatel = $this->getUser();

        if (!$uzivatel) {
            return $this->redirectToRoute('app_login');
        }

        $aukce = $aukceRepository->findOneBy(['verejneId' => $verejneId]);

        if (!$aukce) {
            throw $this->createNotFoundException('Aukce nebyla nalezena.');
        }

        if (!$this->isCsrfTokenValid(
            'spustit_'.$aukce->getVerejneId(),
            $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException('Neplatný CSRF token.');
        }

        if (
            $aukce->getUzivatel()->getId() !== $uzivatel->getId()
            && !$this->isGranted('ROLE_ADMIN')
        ) {
            throw $this->createAccessDeniedException('Tuto aukci může spustit pouze autor nebo administrátor.');
        }

        if ($aukce->getStav() !== 'ukoncena') {
            $this->addFlash('error', 'Spustit lze pouze ukončenou aukci.');
            return $this->redirectToRoute('aukce_detail', ['verejneId' => $aukce->getVerejneId()]);
        }

        if ($aukce->getCasKonce() <= new \DateTime()) {
            $novyKonec = (new \DateTime())->modify('+7 days');
            $aukce->setCasKonce($novyKonec);
        }

        $aukce->setStav('aktivni');
        // smazání starých notifikací o výhře
        $notifikaceRepo = $entityManager->getRepository(\App\Entity\Notifikace::class);

        $notifikace = $notifikaceRepo->findBy([
            'typ' => 'vyhra',
        ]);

        foreach ($notifikace as $n) {
            if (str_contains($n->getText(), $aukce->getNazev())) {
                $entityManager->remove($n);
            }
        }
        $entityManager->flush();

        $this->addFlash('success', 'Aukce byla znovu spuštěna.');
        return $this->redirectToRoute('aukce_detail', ['verejneId' => $aukce->getVerejneId()]);
    }
    #[Route('/aukce/{verejneId}/smazat', name: 'aukce_smazat', methods: ['POST'])]
    public function smazatAukci(string $verejneId, Request $request, AukceRepository $aukceRepository, ReportAukceRepository $reportAukceRepository, SazkyRepository $sazkyRepository, KomentareRepository $komentareRepository, EntityManagerInterface $entityManager): Response
    {
        $aukce = $aukceRepository->findOneBy(['verejneId' => $verejneId]);

        if (!$aukce) {
            throw $this->createNotFoundException('Aukce nebyla nalezena.');
        }

        $uzivatel = $this->getUser();

        if (!$uzivatel) {
            return $this->redirectToRoute('app_login');
        }

        // CSRF ochrana
        if (!$this->isCsrfTokenValid(
            'smazat_' . $verejneId,
            $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException('Neplatný CSRF token.');
        }

        // oprávnění
        if ($aukce->getUzivatel()->getId() !== $uzivatel->getId() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        // smazání reportů
        $reporty = $reportAukceRepository->findBy(['aukce' => $aukce]);
        foreach ($reporty as $report) {
            $entityManager->remove($report);
        }

        //smazání sázek
        $sazky = $sazkyRepository->findBy(['aukce' => $aukce]);
        foreach ($sazky as $sazka) {
            $entityManager->remove($sazka);
        }

        // smazání komentářů
        $komentare = $komentareRepository->findBy(['aukce' => $aukce]);
        foreach ($komentare as $komentar) {
            $entityManager->remove($komentar);
        }

        // smazání plateb
        $platby = $entityManager->getRepository(\App\Entity\Platby::class)->findBy(['aukce' => $aukce]);
        foreach ($platby as $platba) {
            $entityManager->remove($platba);
        }

        // smazání všech fotek
        $fotky = $entityManager->getRepository(\App\Entity\FotkyAukci::class)->findBy(['aukce' => $aukce]);
        foreach ($fotky as $fotka) {
            $cesta = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $fotka->getCesta();

            if (file_exists($cesta)) {
                unlink($cesta);
            }

            $entityManager->remove($fotka);
        }

        // smazání hlavní fotky
        if ($aukce->getHlavniFoto()) {
            $hlavni = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $aukce->getHlavniFoto();

            if (file_exists($hlavni)) {
                unlink($hlavni);
            }
        }

        // smazání aukce
        $entityManager->remove($aukce);
        $entityManager->flush();

        $this->addFlash('success', 'Aukce byla smazána.');

        return $this->redirectToRoute('aukce_moje');
    }
    #[Route('/aukce/fotka/{id}/smazat', name: 'smazat_fotku_aukce', methods: ['POST'])]
    public function smazatFotkuAukce(int $id, Request $request, EntityManagerInterface $entityManager): Response {
        $uzivatel = $this->getUser();

        if (!$uzivatel) {
            return $this->redirectToRoute('app_login');
        }
        if (!$this->isCsrfTokenValid('smazat_fotku_'.$id, $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Neplatný CSRF token.');
        }
        $fotka = $entityManager->getRepository(FotkyAukci::class)->find($id);

        if (!$fotka) {
            throw $this->createNotFoundException('Fotka nebyla nalezena.');
        }

        $aukce = $fotka->getAukce();

        // kontrola oprávnění
        if (
            $aukce->getUzivatel()->getId() !== $uzivatel->getId()
            && !$this->isGranted('ROLE_ADMIN')
        ) {
            throw $this->createAccessDeniedException();
        }

        // smazání souboru
        $cesta = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $fotka->getCesta();

        if (file_exists($cesta)) {
            unlink($cesta);
        }

        // pokud byla hlavní fotka → zrušit ji
        if ($aukce->getHlavniFoto() === $fotka->getCesta()) {
            $aukce->setHlavniFoto(null);
        }

        // smazání z DB
        $entityManager->remove($fotka);
        $entityManager->flush();

        $this->addFlash('success', 'Fotka byla smazána.');

        return $this->redirectToRoute('aukce_upravit', [
            'verejneId' => $aukce->getVerejneId()
        ]);
    }
}
