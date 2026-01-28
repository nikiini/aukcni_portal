<?php
namespace App\Controller;

use App\Repository\NotifikaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class NotifikaceController extends AbstractController{
    #[Route ('/notifikace', name:'notifikace')]
    public function index(NotifikaceRepository $notifikaceRepository, EntityManagerInterface $entityManager): Response{
        $uzivatel = $this->getUser();
        if(!$uzivatel){
            return $this->redirectToRoute('app_login');
        }
        $notifikace = $notifikaceRepository->findBy([
            'uzivatel' => $uzivatel],
            ['vytvoreno' => 'DESC']
        );

        foreach($notifikace as $jednaNotifikace){
            if($jednaNotifikace->getStav() === 'nova'){
                $jednaNotifikace->setStav('prectena');
            }
        }
        $entityManager->flush();
        return $this->render('notifikace/index.html.twig', ['notifikace' => $notifikace]);
    }

}

