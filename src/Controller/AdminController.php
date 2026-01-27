<?php
namespace App\Controller;
use App\Repository\AukceRepository;
use App\Repository\KomentareRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

#[Route('/admin')]
class AdminController extends AbstractController{
    #[Route('/aukce', name: 'admin_aukce')]
    public function aukce(AukceRepository $aukceRepository): Response{
        return $this->render('admin/aukce.html.twig', [
            'aukce' => $aukceRepository->findAll()
        ]);
    }
    #[Route('/komentar/{id}/smazat', name: 'admin_komentar_smazat')]
    public function smazatKomentar(int $id, KomentareRepository $komentareRepository, EntityManagerInterface $entityManager): Response
    {
        $komentar =  $komentareRepository->find($id);
        if($komentar){
            $entityManager->remove($komentar);
            $entityManager->flush();
            $this->addFlash('success', 'Komenář byl smazán.');
        }else{
            $this->addFlash('error', 'Komentář nebyl nalezen.');
        }
        return $this->redirectToRoute('aukce_detail', ['id' => $komentar ? $komentar->getAukce()->getId() : null]);
    }
    #[Route('/aukce/{id}/smazat', name:'admin_aukce_smazat')]
    public function smazatAukci(int $id, AukceRepository $aukceRepository, EntityManagerInterface $entityManager): Response{
        $aukce =  $aukceRepository->find($id);
        if($aukce){
            $entityManager->remove($aukce);
            $entityManager->flush();
            $this->addFlash('success','Aukce byla smazána.');
        }else{
            $this->addFlash('error', 'Aukce nebyla nalezena.');
        }
        return $this->redirectToRoute('admin_aukce');
    }
}