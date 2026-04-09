<?php
namespace App\Controller;
use App\Repository\AukceRepository;
use App\Repository\UzivatelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Form\ProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class ProfilController extends AbstractController{
    //  Zobrazí profil přihlášeného uživatele.
    #[Route('/profil', name: 'profil')]
    public function index(AukceRepository $aukceRepository, UzivatelRepository $uzivatelRepository): Response
    {
        $prihlasenyUzivatel = $this->getUser();
        if(!$prihlasenyUzivatel){
            return $this->redirectToRoute('app_login');
        }
        $uzivatel = $uzivatelRepository->find($prihlasenyUzivatel->getId());
        $pocetAktivnichAukci = $aukceRepository
            ->pocetAktivnichAukciUzivatele($uzivatel->getId());
        $pocetUkoncenychAukci = $aukceRepository
            ->pocetUkoncenychAukciUzivatele($uzivatel->getId());
        return $this->render('profil/index.html.twig',['uzivatel'=> $uzivatel, 'pocetAktivnichAukci'=> $pocetAktivnichAukci, 'pocetUkoncenychAukci'=> $pocetUkoncenychAukci]);
    }
    //  Umožní upravit údaje profilu včetně profilové fotografie.
    #[Route('/profil/uprava', name: 'profil_uprava')]
    public function uprava(Request $request, EntityManagerInterface $entityManager, UzivatelRepository $uzivatelRepository): Response
    {
        $prihlasenyUzivatel = $this->getUser();

        if (!$prihlasenyUzivatel) {
            return $this->redirectToRoute('app_login');
        }

        $uzivatel = $uzivatelRepository->find($prihlasenyUzivatel->getId());

        if (!$uzivatel) {
            throw $this->createNotFoundException('Uživatel nebyl nalezen.');
        }

        $formular = $this->createForm(ProfilType::class, $uzivatel);
        $formular->handleRequest($request);

        if ($formular->isSubmitted() && $formular->isValid()) {
            $existujici = $uzivatelRepository->findOneBy([
                'uzivatelske_jmeno' => $uzivatel->getUzivatelskeJmeno()
            ]);

            if ($existujici && $existujici->getId() !== $uzivatel->getId()) {
                $this->addFlash('error', 'Toto uživatelské jméno už existuje.');
                return $this->redirectToRoute('profil_uprava');
            }

            $foto = $formular->get('profilFoto')->getData();

            if ($foto) {
                if ($uzivatel->getProfilFoto()) {
                    $staraCesta = $this->getParameter('kernel.project_dir') . '/public/uploads/profily/' . $uzivatel->getProfilFoto();

                    if (file_exists($staraCesta)) {
                        unlink($staraCesta);
                    }
                }

                $novyNazev = uniqid() . '.' . ($foto->guessExtension() ?: 'jpg');

                $foto->move(
                    $this->getParameter('kernel.project_dir') . '/public/uploads/profily',
                    $novyNazev
                );

                $uzivatel->setProfilFoto($novyNazev);
            }

            $entityManager->flush();
            $this->addFlash('success', 'Profil byl úspěšně upraven.');

            return $this->redirectToRoute('profil');
        }

        return $this->render('profil/uprava.html.twig', [
            'formular' => $formular->createView(),
            'uzivatel' => $uzivatel,
        ]);
    }
    #[Route('/profil/foto/smazat', name: 'profil_foto_smazat', methods: ['POST'])]
    public function smazatFoto(Request $request, EntityManagerInterface $entityManager): Response
    {
        $uzivatel = $this->getUser();

        if (!$uzivatel) {
            return $this->redirectToRoute('app_login');
        }

        if (!$this->isCsrfTokenValid('smazat_foto', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Neplatný CSRF token.');
        }

        if ($uzivatel->getProfilFoto()) {

            $cesta = $this->getParameter('kernel.project_dir') . '/public/uploads/profily/' . $uzivatel->getProfilFoto();

            if (file_exists($cesta)) {
                unlink($cesta);
            }

            $uzivatel->setProfilFoto(null);
            $entityManager->flush();
        }

        $this->addFlash('success', 'Profilová fotografie byla smazána.');

        return $this->redirectToRoute('profil_uprava');
    }
}