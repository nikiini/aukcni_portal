<?php
namespace App\Controller;

use App\Entity\Aukce;
use App\Entity\Notifikace;
use App\Form\ProfilType;
use App\Repository\AukceRepository;
use App\Repository\KomentareRepository;
use App\Repository\ReportAukceRepository;
use App\Repository\SazkyRepository;
use App\Repository\UzivatelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/*Toto je řadič administátorské části aplikace.*/
#[Route('/admin')]
class AdminController extends AbstractController{
    //   Zobrazí hlavní administraci a přehled základních dat systému
    #[Route('/panel', name: 'admin_panel')]
    #[Route('/uzivatele', name:'admin_uzivatele' )]
    public function panel(Request $request, UzivatelRepository $uzivatelRepository,AukceRepository $aukceRepository, SazkyRepository $sazkyRepository, KomentareRepository $komentareRepository): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $hledani = trim((string)$request->query->get('q', ''));
        $vybranyUzivatelId = $request->query->get('u');

        $dotazNaUzivatele = $uzivatelRepository->createQueryBuilder('uzivatel')
            ->orderBy('uzivatel.vytvoreno', 'DESC');

        if ($hledani !== '') {
            $dotazNaUzivatele->andWhere(
                'uzivatel.cele_jmeno LIKE :hledani 
                 OR uzivatel.uzivatelske_jmeno LIKE :hledani 
                 OR uzivatel.email LIKE :hledani'
            )
                ->setParameter('hledani', '%' . $hledani . '%');
        }

        $uzivatele = $dotazNaUzivatele->getQuery()->getResult();

        $vybranyUzivatel = null;
        if ($vybranyUzivatelId) {
            $vybranyUzivatel = $uzivatelRepository->findOneBy(['verejneId' => $vybranyUzivatelId]);
        }
        if (!$vybranyUzivatel && !empty($uzivatele)) {
            $vybranyUzivatel = $uzivatele[0];
        }

        $aukceUzivatele = [];
        $sazkyUzivatele = [];
        $komentareUzivatele = [];

        if ($vybranyUzivatel) {
            $aukceUzivatele = $aukceRepository->findBy(
                ['uzivatel' => $vybranyUzivatel],
                ['cas_zacatku' => 'DESC']
            );

            $sazkyUzivatele = $sazkyRepository->findBy(
                ['uzivatel' => $vybranyUzivatel],
                ['vytvoreno' => 'DESC']
            );

            $komentareUzivatele = $komentareRepository->findBy(
                ['uzivatel' => $vybranyUzivatel],
                ['vytvoreno' => 'DESC']
            );
        }
        //předání dat do šablony administrace
        return $this->render('admin/panel.html.twig', [
            'uzivatele' => $uzivatele,
            'vybranyUzivatel' => $vybranyUzivatel,
            'aukceUzivatele' => $aukceUzivatele,
            'sazkyUzivatele' => $sazkyUzivatele,
            'komentareUzivatele' => $komentareUzivatele,
            'hledani' => $hledani,
        ]);
    }
    //  Načte seznam nahlášení problémových aukcí pro administrátora.
    #[Route('/reporty', name: 'admin_reporty')]
    public function reporty(ReportAukceRepository $reportAukceRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $reporty = $reportAukceRepository->findBy(
            [],
            ['vytvoreno' => 'DESC']
        );

        return $this->render('admin/reporty.html.twig', [
            'reporty' => $reporty
        ]);
    }
    //  Zobrazí administrátorovi přehled všech aukcí.
    #[Route('/aukce', name: 'admin_aukce')]
    public function aukce(AukceRepository $aukceRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/panel.html.twig', [
            'aukce' => $aukceRepository->findAll(),
            'uzivatele' => [],
            'vybranyUzivatel' => null,
            'aukceUzivatele' => [],
            'sazkyUzivatele' => [],
            'komentareUzivatele' => [],
            'hledani' => '',
        ]);
    }
    // Umožní administátorovi upravit vybranou aukci.
    #[Route('/aukce/{verejneId}/upravit', name: 'admin_aukce_upravit')]
    public function upravitAukci(string $verejneId, Request $request, AukceRepository $aukceRepository, EntityManagerInterface $entityManager): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $aukce = $aukceRepository->findOneBy(['verejneId' => $verejneId]);

        if (!$aukce) {
            $this->addFlash('error', 'Aukce nebyla nalezena.');
            return $this->redirectToRoute('admin_panel');
        }

        $form = $this->createForm(\App\Form\AukceType::class, $aukce);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $foto = $form->get('fotky')->getData();
            if ($foto) {
                $novyNazev = uniqid() . '.' . $foto->guessExtension();

                $foto->move(
                    $this->getParameter('kernel.project_dir') . '/public/uploads',
                    $novyNazev
                );

                $aukce->setHlavniFoto($novyNazev);
            }
            $pocetSazek = count($aukce->getSazky());
            if ($pocetSazek === 0) {
                $aukce->setAktualniCena($aukce->getVychoziCena());
            }
            $entityManager->flush();
            $this->addFlash('success', 'Aukce byla upravena.');
            return $this->redirectToRoute('admin_panel');
        }
        return $this->render('admin/upravit_aukci.html.twig', [
            'form' => $form->createView(),
            'aukce' => $aukce,
        ]);
    }
    //  Odsstraní aukci z administrace.
    #[Route('/aukce/{verejneId}/smazat', name:'admin_aukce_smazat', methods:['POST'])]
    public function smazatAukci(string $verejneId, AukceRepository $aukceRepository, EntityManagerInterface $entityManager, Request $request): Response{
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $aukce = $aukceRepository->findOneBy(['verejneId' => $verejneId]);
        if (!$aukce) {
            $this->addFlash('error', 'Aukce nebyla nalezena.');
            return $this->redirectToRoute('admin_panel');
        }
        if (!$this->isCsrfTokenValid('smaz_aukci_' . $aukce->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Neplatný CSRF token.');
        }

        $entityManager->remove($aukce);
        $entityManager->flush();

        $this->addFlash('success', 'Aukce byla odstraněna.');
        return $this->redirectToRoute('admin_panel');
    }
    //  Zablokuje uživatele, aby nemohl dále používat systém.
    #[Route('/uzivatel/{verejneId}/blokovat', name: 'admin_uzivatel_blokovat', methods: ['POST'])]
    public function blokovatUzivatele(string $verejneId, EntityManagerInterface $entityManager, UzivatelRepository $uzivatelRepository, Request $request): Response{
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $uzivatel = $uzivatelRepository->findOneBy(['verejneId' => $verejneId]);
        if(!$uzivatel){
            $this->addFlash('error', 'Uzivatel nebyl nalezen.');
            return $this->redirectToRoute('admin_uzivatele');
        }
        if (!$this->isCsrfTokenValid('blokuj_' . $uzivatel->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Neplatný CSRF token.');
        }
        if ($uzivatel->getRole() === 'ROLE_ADMIN'){
            $this->addFlash('error', 'Administátora nelze zablokovat.');
            return $this->redirectToRoute('admin_uzivatele', ['u' => $verejneId]);
        }
        $duvod = trim((string)$request->request->get('duvod', ''));
        $uzivatel->setBlokovan(true);
        $text = 'Váš účet byl administrátorem zablokován. Pokud si myslíte, že došlo k omylu, kontaktujte podporu.';
        if ($duvod !== '') {
            $text .= ' Důvod: ' . $duvod . '.';
        }
        $notifikace = new Notifikace();
        $notifikace->setUzivatel($uzivatel);
        $notifikace->setTyp('blokace');
        $notifikace->setStav('nova');
        $notifikace->setText($text);
        $notifikace->setVytvoreno(new \DateTime());

        $entityManager->persist($notifikace);
        $entityManager->flush();

        $this->addFlash('success', 'Uživatel byl zablokován.');
        return $this->redirectToRoute('admin_uzivatele', ['u' => $verejneId]);
    }
    //  Zruší blokaci vybraného uživatele.
    #[Route('/uzivatel/{verejneId}/odblokovat', name: 'admin_uzivatel_odblokovat', methods: ['POST'])]
    public function odblokovatUzivatele(string $verejneId, EntityManagerInterface $entityManager, UzivatelRepository $uzivatelRepository, Request $request): Response{
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $uzivatel = $uzivatelRepository->findOneBy(['verejneId' => $verejneId]);
        if(!$uzivatel){
            $this->addFlash('error', 'Uzivatel nebyl nalezen.');
            return $this->redirectToRoute('admin_uzivatele');
        }
        if (!$this->isCsrfTokenValid('odblokuj_' . $uzivatel->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Neplatný CSRF token.');
        }
        $uzivatel->setBlokovan(false);
        $entityManager->flush();

        $this->addFlash('success', 'Uživatel byl odblokován.');
        return $this->redirectToRoute('admin_uzivatele', ['u' => $verejneId]);
    }
    //  Umožní administrátorovi upravit údaje uživatele.
    #[Route('/uzivatel/{verejneId}/upravit', name: 'admin_uzivatel_upravit')]
    public function upravitUzivatele(string $verejneId, Request $request, UzivatelRepository $uzivatelRepository, EntityManagerInterface $entityManager): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $uzivatel = $uzivatelRepository->findOneBy(['verejneId' => $verejneId]);
        if (!$uzivatel) {
            $this->addFlash('error', 'Uživatel nebyl nalezen.');
            return $this->redirectToRoute('admin_uzivatele');
        }

        $formular = $this->createForm(ProfilType::class, $uzivatel);
        $formular->handleRequest($request);

        if ($formular->isSubmitted() && $formular->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Profil uživatele byl upraven.');
            return $this->redirectToRoute('admin_uzivatele', ['u' => $verejneId]);
        }

        return $this->render('admin/upravit_uzivatele.html.twig', [
            'formular' => $formular->createView(),
            'uzivatel' => $uzivatel,
        ]);
    }
    //  Ruční zastavení aukce z administrace.
    #[Route('/aukce/{id}/zastavit', name: 'admin_zastavit_aukci')]
    public function zastavitAukci(Aukce $aukce, EntityManagerInterface $em): Response
    {
        $aukce->setStav('zastavena');
        $em->flush();

        $this->addFlash('success', 'Aukce byla zastavena.');

        return $this->redirectToRoute('admin_panel');
    }
    //  Ruční opětovné spuštění aukce z administrace.
    #[Route('/aukce/{id}/spustit', name: 'admin_spustit_aukci')]
    public function spustitAukci(Aukce $aukce, EntityManagerInterface $em): Response
    {
        $aukce->setStav('aktivni');
        $em->flush();

        $this->addFlash('success', 'Aukce byla spuštěna.');

        return $this->redirectToRoute('admin_panel');
    }
}