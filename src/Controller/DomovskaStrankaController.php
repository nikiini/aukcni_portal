<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AukceRepository;
class DomovskaStrankaController extends AbstractController{
    #[Route('/', name: 'domovskaStranka')]
    public function index(AukceRepository $aukceRepository): Response{
        $seznamNovychAukci = $aukceRepository->najdiNoveAukce(6);
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