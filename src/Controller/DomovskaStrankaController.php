<?php
namespace App\Controller;
use App\Repository\AukceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DomovskaStrankaController extends AbstractController{
    //  Načte data pro domovskou stránku a zobrazí hlavní přehled aukcí.
    #[Route('/', name: 'domovskaStranka')]
    public function index(AukceRepository $aukceRepository): Response{
        $seznamNovychAukci = $aukceRepository->najdiDoporucene(6);
        $seznamAukciNoveDnes = $aukceRepository->najdiNoveAukceDnes(6);
        $seznamAukciKonciBrzy = $aukceRepository->najdiAukceKonciBrzy(6);

        return $this->render('domovska_stranka/index.html.twig', [
            'aukce' => $seznamNovychAukci,
            'aukceNoveDnes' => $seznamAukciNoveDnes,
            'aukceKonciBrzy' => $seznamAukciKonciBrzy,
            'uzivatel' => $this->getUser()
        ]);
    }
}