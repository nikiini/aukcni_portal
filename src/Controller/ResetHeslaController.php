<?php
 namespace App\Controller;

 use App\Entity\Uzivatel;
 use Doctrine\ORM\EntityManagerInterface;
 use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
 use Symfony\Component\HttpFoundation\Request;
 use Symfony\Component\HttpFoundation\Response;
 use Symfony\Component\Mailer\MailerInterface;
 use Symfony\Component\Mime\Email;
 use Symfony\Component\Routing\Annotation\Route;
 use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
 use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

 class ResetHeslaController extends AbstractController
 {
     #[Route('/reset-hesla', name: 'reset_hesla_zadost')]
     public function zadost(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
     {
         if ($request->isMethod('POST')) {
             $emailInput = trim((string)$request->request->get('email'));

             if ($emailInput !== '') {
                 $this->addFlash('error', 'Zadejte e-mail.');
                 return $this->redirectToRoute('reset_hesla_zadost');
             }
             $uzivatel = $entityManager->getRepository(Uzivatel::class)->findOneBy(['email' => $emailInput]);

             if (!$uzivatel) {
                 $this->addFlash('error', 'Účet s tímto e-mailem nenalezen.');
                 return $this->redirectToRoute('reset_hesla_zadost');
             }

             $token = bin2hex(random_bytes(32));
             $expiresAt = (new \DateTime())->modify('+1 hour');

             $uzivatel->setResetToken($token);
             $uzivatel->setResetTokenExpiresAt($expiresAt);
             $entityManager->flush();

             $resetUrl = $this->generateUrl('reset_hesla_nove', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

             $email = (new Email())
                 ->from('no-reply@aukce.test')
                 ->to($uzivatel->getEmail())
                 ->subject('Obnovení hesla')
                 ->html($this->renderView('security/email_reset_hesla.html.twig', ['uzivatel' => $uzivatel, 'resetUrl' => $resetUrl]));

             $mailer->send($email);

             $this->addFlash('success', 'Na Váš e-mail byl odeslán odkaz pro obnovení hesla.');
             return $this->redirectToRoute('app_login');
         }

         return $this->render('security/reset_hesla_zadost.html.twig');
     }

     #[Route('/reset-hesla/{token}', name: 'reset_hesla_nove')]
     public function nove(string $token, Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response{
         $uzivatel = $entityManager->getRepository(Uzivatel::class)->findOneBy(['reset_token' => $token]);
         if (!$uzivatel) {
             $this->addFlash('error', 'Neplatný nebo již použitý odkaz.');
             return $this->redirectToRoute('app_login');
         }
         $ted = new \DateTime();
         if($uzivatel->getResetTokenExpiresAt() === null || $uzivatel->getResetTokenExpiresAt() < $ted){
             $this->addFlash('error', 'Platnost odkazu vypršela. Požádejte o nový.');
             $uzivatel->setResetToken(null);
             $uzivatel->setResetTokenExpiresAt(null);
             $entityManager->flush();

             return $this->redirectToRoute('reset_hesla_zadost');
         }

         if($request->isMethod('POST')) {
             $heslo = (string)$request->request->get('heslo');
             $heslo2 = (string)$request->request->get('heslo2');

             if($heslo === '' || $heslo2 === ''){
                 $this->addFlash('error','Vyplňte obě pole hesla.');
                 return $this->redirectToRoute('reset_hesla_nove', ['token' => $token]);
             }
             if($heslo !== $heslo2){
                 $this->addFlash('error', 'Hesla se neshodují.');
                 return $this->redirectToRoute('reset_hesla_nove', ['token' => $token]);
             }
             if(mb_strlen($heslo)<8 ){
                 $this->addFlash('error', 'Heslo musí mít alespoň 8 znaků.');
                 return $this->redirectToRoute('reset_hesla_nove', ['token' => $token]);
             }
             $uzivatel->setHeslo($passwordHasher->hashPassword($uzivatel, $heslo));
             $uzivatel->setResetToken(null);
             $uzivatel->setResetTokenExpiresAt(null);

             $entityManager->flush();

             $this->addFlash('success', 'Heslo bylo úspěšně změněno.');
             return $this->redirectToRoute('app_login');
         }
         return $this->render('security/reset_hesla_nove.html.twig', [
             'token' => $token,
         ]);
     }
 }