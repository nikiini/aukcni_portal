<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController{
    #[Route('/login', name: 'app_login')]
public function login(AuthenticationUtils $authenticationUtils): Response
    {
        //je-li uživatel přihlášený, tak ho pustíme na login, pokud není přihlášený tak ho nepustíme.
        if($this->getUser()){
            return $this->redirectToRoute('domovskaStranka'); //musí existovat!
        }
        //potvrzení o odeslaném emailu, pokud je nějaká chyba přihlášení, ukáže se.
        $posledniUsername = $authenticationUtils->getLastUsername();
        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('security/login.html.twig', [
            'posledni_username' => $posledniUsername,
            'error' => $error,
        ]);
    }
    #[Route('/logout', name: 'app_logout')]
public function logout(): void{
        //symfony si to samo vyřeší, tato metoda body nesmí nic dělat.
    }
}