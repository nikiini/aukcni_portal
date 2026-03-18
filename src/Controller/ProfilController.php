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
    public function uprava(Request $request, EntityManagerInterface $entityManager): Response
    {
        $uzivatel = $this->getUser();

        if (!$uzivatel) {
            return $this->redirectToRoute('app_login');
        }

        $formular = $this->createForm(ProfilType::class, $uzivatel);
        $formular->handleRequest($request);

        if ($formular->isSubmitted() && $formular->isValid()) {
            $foto = $formular->get('profilFoto')->getData();

            if ($foto) {
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
        ]);
    }
}