<?php
namespace App\Controller;
use App\Entity\Uzivatel;
use App\Form\RegistraceFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistraceController extends AbstractController{
    #[Route('/registrace', name: 'app_registrace')]
public function registrace(
    Request $request,
    EntityManagerInterface $em,
    UserPasswordHasherInterface $passwordHasher
    ): Response{
        $uzivatel = new Uzivatel();
        $form = $this->createForm(RegistraceFormType::class, $uzivatel);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            //Hashování hesla:
            $heslo = $form->get('heslo')->getData();
            $uzivatel->setHeslo(
                $passwordHasher->hashPassword($uzivatel, $heslo)
            );
            $uzivatel->setCeleJmeno($uzivatel->getUzivatelskeJmeno());
        $uzivatel->setKredity("0.00");
        $uzivatel->setRole("standard");
        $uzivatel->setEmailOvereno(false);
        $uzivatel->setBlokovan(false);
        $uzivatel->setVytvoreno(new \DateTime());

        $em->persist($uzivatel);
        $em->flush();

        $this->addFlash('success', 'Registrace proběhla úspěšně. Nnyní se můžete přihlásit.');
        return $this->redirectToRoute('app_login');
    }

    return $this->render('security/registrace.html.twig', [
        'registraceForm' => $form->createView(),
    ]);
    }
}
