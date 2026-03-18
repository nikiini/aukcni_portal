<?php
namespace App\Controller;

use App\Entity\Uzivatel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController{
    //  Zobrazí přihlašovací formulář a předá případnou chybu autentizace do šablony.
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        if($this->getUser()){
            return $this->redirectToRoute('domovskaStranka');
        }
        $posledniUsername = $authenticationUtils->getLastUsername();
        $error = $authenticationUtils->getLastAuthenticationError();

        if($error && $posledniUsername){
            $repozitar = $entityManager->getRepository(Uzivatel::class);

            // Nejdřív zkusíme najít uživatele podle e-mailu
            $uzivatel = $repozitar->findOneBy(['email' => $posledniUsername]);

            // Když neexistuje, zkusíme podle uživatelského jména
            if (!$uzivatel) {
                $uzivatel = $repozitar->findOneBy(['uzivatelske_jmeno' => $posledniUsername]);
            }

            if($uzivatel && !$uzivatel->isEmailOvereno()){
                $vytvoreno = $uzivatel->getVytvoreno();
                $ted = new \DateTime();
                $rozdil = $vytvoreno->diff($ted);

                if($rozdil->days >= 3){
                    $emailObj = (new Email())
                        ->from($_ENV['MAILER_FROM'] ?? 'noreply@aukcni-portal.cz')
                        ->to($uzivatel->getEmail())
                        ->subject('Účet byl smazán')
                        ->html($this->renderView(
                                'security/email_ucet_smazan.html.twig',
                                [
                                    'jmeno' => $uzivatel->getCeleJmeno(),
                                    'email' => $uzivatel->getEmail(),
                                    'domena' => 'wurknerova.dev.spsostrov.cz'
                                ]
                            )
                        );

                    try {
                        $mailer->send($emailObj);
                    } catch (\Throwable $e) {
                        // účet se i tak smaže, ale aplikace nespadne
                    }

                    $entityManager->remove($uzivatel);
                    $entityManager->flush();

                    $this->addFlash('error', 'Účet byl smazán z důvodu neověření e-mailu.');
                    return $this->redirectToRoute('app_login');
                }

                $this->addFlash('error', 'Nejprve ověřte svůj e-mail.');
            }
        }

        return $this->render('security/login.html.twig', [
            'posledni_username' => $posledniUsername,
            'error' => $error,
        ]);
    }
    //  Route zachycená firewallem pro odhlášení uživatele.
    #[Route('/logout', name: 'app_logout')]
    public function logout(): void{
        //symfony si samo vyřeší odhlášení, tato metoda body nesmí nic dělat.
    }
}