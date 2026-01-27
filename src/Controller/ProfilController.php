<?php
namespace App\Controller;
use App\Repository\AukceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Form\ProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class ProfilController extends AbstractController{
    #[Route('/profil', name: 'profil')]
    public function index(AukceRepository $aukceRepository): Response
    {
        $uzivatel = $this->getUser();
        if(!$uzivatel){
            return $this->redirectToRoute('app_login');
        }
        $pocetAktivnichAukci = $aukceRepository
            ->pocetAktivnichAukciUzivatele($uzivatel->getId());
        $pocetUkoncenychAukci = $aukceRepository
            ->pocetUkoncenychAukciUzivatele($uzivatel->getId());
        return $this->render('profil/index.html.twig',['uzivatel'=> $uzivatel, 'pocetAktivnichAukci'=> $pocetAktivnichAukci, 'pocetUkoncenychAukci'=> $pocetUkoncenychAukci]);
    }
    #[Route('/profil/uprava', name: 'profil_uprava')]
    public function uprava(Request $request, EntityManagerInterface $entityManager): Response
    {
        $uzivatel = $this->getUser();
        if(!$uzivatel){
            return $this->redirectToRoute('app_login');
        }
        $formular = $this->createForm(ProfilType::class, $uzivatel);
        $formular->handleRequest($request);
        if($formular->isSubmitted() && $formular->isValid()){
            $entityManager->flush();
            $this->addFlash('success', 'Profil byl úspěšně upraven.');
            return $this->redirectToRoute('profil');
        }
        return $this->render('profil/uprava.html.twig', [
            'formular' => $formular->createView(),

        ]);
    }
}