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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;


class RegistraceController extends AbstractController{
    //  Zpracuje registraci nového uživatele, vytvoří token a odešle ověřovací e-mail.
    #[Route('/registrace', name: 'app_registrace')]
    public function registrace(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, MailerInterface $mailer): Response{
        $uzivatel = new Uzivatel();
        $uzivatel->setVerejneId(bin2hex(random_bytes(8)));
        $form = $this->createForm(RegistraceFormType::class, $uzivatel);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $existujeEmail = $entityManager->getRepository(Uzivatel::class)
                ->findOneBy(['email' => $uzivatel->getEmail()]);
            if($existujeEmail){
                $this->addFlash('error', 'Tento e-mail je již registrován.');
                return $this->redirectToRoute('app_registrace');
            }
            $existujeUzivatelskeJmeno = $entityManager->getRepository(Uzivatel::class)
                ->findOneBy(['uzivatelske_jmeno' => $uzivatel->getUzivatelskeJmeno()]);

            if($existujeUzivatelskeJmeno){
                $this->addFlash('error', 'Toto uživatelské jméno je již používano.');
                return $this->redirectToRoute('app_registrace');
            }
            $existujeCeleJmeno = $entityManager->getRepository(Uzivatel::class)
                ->findOneBy(['cele_jmeno' => $uzivatel->getCeleJmeno()]);

            if($existujeCeleJmeno){
                $this->addFlash('error', 'Toto celé jméno je již používáno.');
                return $this->redirectToRoute('app_registrace');
            }
            $souhlas18 = $request->request->get('souhlas18');
            if(!$souhlas18){
                $this->addFlash('error', 'Pro registraci musíte potvrdit, že je Vám alespoň 18 let.');
                return $this->redirectToRoute('app_registrace');
            }
            $heslo = $form->get('heslo')->getData();
            $uzivatel->setHeslo(
                $passwordHasher->hashPassword($uzivatel, $heslo)
            );
            $token = bin2hex(random_bytes(32));
            $uzivatel->setEmailToken($token);
        $uzivatel->setKredity("0.00");
        $uzivatel->setRole('ROLE_USER');
        $uzivatel->setEmailOvereno(false);
        $uzivatel->setBlokovan(false);
        $uzivatel->setVytvoreno(new \DateTime());

        $celeJmeno = $uzivatel->getCeleJmeno();
        $uzivatel->setCeleJmeno(mb_convert_case($celeJmeno, MB_CASE_TITLE, "UTF-8"));

        $entityManager->persist($uzivatel);
        $entityManager->flush();


            $link = $this->generateUrl('overeni_emailu', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

            $emailObj = (new Email())
                ->from('tester.aukce@gmail.com')
                ->to($uzivatel->getEmail())
                ->subject('Ověření e-mailu - Aukční portál')
                ->html($this->renderView('security/email_overeni.html.twig', [
                    'uzivatel' => $uzivatel,
                    'overovaciUrl' => $link
                ]));

            try {
                $mailer->send($emailObj);
            } catch (\Throwable $e) {
                $this->addFlash('error', 'Registrace proběhla, ale nepodařilo se odeslat ověřovací e-mail.');
                return $this->redirectToRoute('app_login');
            }

            $this->addFlash('success', 'Registrace proběhla úspěšně. Zkontrolujte svůj e-mail pro aktivaci účtu.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/registrace.html.twig', [
            'registraceForm' => $form->createView(),
        ]);
    }
    //  Potvrdí e-mail uživatele pomocí tokenu z odkazu.
    #[Route('/overeni-emailu/{token}', name: 'overeni_emailu')]
    public function overeniEmailu(string $token, EntityManagerInterface $entityManager): Response{
        $uzivatel = $entityManager->getRepository(Uzivatel::class)
            ->findOneBy(['emailToken' => $token]);
        if(!$uzivatel){
            $this->addFlash('error', 'Ověřovací odkaz je neplatný.');
            return $this->redirectToRoute('app_login');
        }
        $uzivatel->setEmailOvereno(true);
        $uzivatel->setEmailToken(null);
        $entityManager->flush();

        $this->addFlash('success', 'E-mail byl úspěšně ověřen. Nyní se můžete přihlásit.');
        return $this->redirectToRoute('app_login');
    }
}
