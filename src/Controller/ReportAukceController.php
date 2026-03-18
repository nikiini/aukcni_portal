<?php

namespace App\Controller;

use App\Entity\ReportAukce;
use App\Repository\AukceRepository;
use App\Repository\ReportAukceRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReportAukceController extends AbstractController
{
    //  Uloží nahlášení problémového průběhu aukce mezi dvěma účastníky.
    #[Route('/aukce/{verejneId}/report', name: 'aukce_report')]
    public function report(string $verejneId, Request $request, AukceRepository $aukceRepository, ReportAukceRepository $reportRepository, EntityManagerInterface $entityManager): Response {
        $uzivatel = $this->getUser();

        if (!$uzivatel) {
            return $this->redirectToRoute('app_login');
        }

        $aukce = $aukceRepository->findOneBy(['verejneId' => $verejneId]);

        if (!$aukce) {
            throw $this->createNotFoundException();
        }

        if ($aukce->getStav() !== 'ukoncena' || !$aukce->getVitez()) {
            $this->addFlash('error', 'Problém lze nahlásit pouze u ukončené aukce s určeným vítězem.');
            return $this->redirectToRoute('aukce_detail', ['verejneId' => $aukce->getVerejneId()]);
        }

        $autor = $aukce->getUzivatel();
        $vitez = $aukce->getVitez();

        $jeAutor = $autor && $autor->getId() === $uzivatel->getId();
        $jeVitez = $vitez && $vitez->getId() === $uzivatel->getId();

        if (!$jeAutor && !$jeVitez) {
            $this->addFlash('error', 'Nahlášení může podat pouze autor aukce nebo její vítěz.');
            return $this->redirectToRoute('aukce_detail', ['verejneId' => $aukce->getVerejneId()]);
        }

        $nahlasenyUzivatel = $jeAutor ? $vitez : $autor;

        $existujeReport = $reportRepository->findOneBy([
            'aukce' => $aukce,
            'nahlasujici' => $uzivatel
        ]);

        if ($existujeReport) {
            $this->addFlash('error', 'Tuto aukci jste již nahlásil(a).');
            return $this->redirectToRoute('aukce_detail', ['verejneId' => $aukce->getVerejneId()]);
        }

        if ($request->isMethod('POST')) {

            if (!$this->isCsrfTokenValid(
                'report_'.$aukce->getVerejneId(),
                $request->request->get('_token')
            )) {
                throw $this->createAccessDeniedException('Neplatný CSRF token.');
            }
            $duvod = trim((string) $request->request->get('duvod', ''));

            if ($duvod === '') {
                $this->addFlash('error', 'Důvod musí být vyplněn a maximálně 300 znaků.');
            } else {
                $report = new ReportAukce();
                $report->setDuvod($duvod);
                $report->setVytvoreno(new \DateTime());
                $report->setAukce($aukce);
                $report->setNahlasujici($uzivatel);
                $report->setNahlaseny($nahlasenyUzivatel);

                try {
                    $entityManager->persist($report);
                    $entityManager->flush();
                } catch (UniqueConstraintViolationException $e) {
                    $this->addFlash('error', 'Tuto aukci jste již nahlásil(a).');
                    return $this->redirectToRoute('aukce_detail', ['verejneId' => $aukce->getVerejneId()]);
                }

                $this->addFlash('success', 'Nahlášení bylo odesláno administrátorovi.');
                return $this->redirectToRoute('aukce_detail', ['verejneId' => $aukce->getVerejneId()]);
            }
        }

        return $this->render('aukce/report.html.twig', [
            'aukce' => $aukce
        ]);
    }
}