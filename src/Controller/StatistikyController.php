<?php
namespace App\Controller;

use App\Repository\AukceRepository;
use App\Repository\KomentareRepository;
use App\Repository\SazkyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatistikyController extends AbstractController{
    #[Route('/statistiky', name: 'uzivatel_statistiky')]
    public function index(AukceRepository $aukceRepository,
                          SazkyRepository $sazkyRepository,
                          KomentareRepository $komentareRepository
    ): Response {
        $uzivatel = $this->getUser();
        if(!$uzivatel){
            return $this->redirectToRoute('app_login');
        }
        $pocetAukciCelkem = $aukceRepository->count(['uzivatel' => $uzivatel]);
        $pocetAukciAktivnich = $aukceRepository->pocetAktivnichAukciUzivatele($uzivatel->getId());
        $pocetAukciUkoncenych = $aukceRepository->pocetUkoncenychAukciUzivatele($uzivatel->getId());

        $pocetSazek = $sazkyRepository->count(['uzivatel' => $uzivatel]);
        $celkemPrihozeno = $sazkyRepository->soucetPrihozuUzivatele($uzivatel->getId());

        $pocetKomentaru = $komentareRepository->count(['uzivatel' => $uzivatel]);

        return $this->render('statistiky/index.html.twig', [
            'uzivatel' => $uzivatel,
            'pocetAukciCelkem' => $pocetAukciCelkem,
            'pocetAukciAktivnich' => $pocetAukciAktivnich,
            'pocetAukciUkoncenych' => $pocetAukciUkoncenych,
            'pocetSazek' => $pocetSazek,
            'celkemPrihozeno' => $celkemPrihozeno,
            'pocetKomentaru' => $pocetKomentaru,
        ]);
    }
}
