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
     // Přijme žádost o reset hesla a odešle uživateli odkaz pro změnu hesla.
     #[Route('/reset-hesla/zadost', name: 'reset_hesla_zadost')]
     public function zadost(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
     {
         if ($request->isMethod('POST')) {
             $email = trim((string)$request->request->get('email', ''));

             if ($email === '') {
                 $this->addFlash('error', 'Zadejte e-mail.');
                 return $this->redirectToRoute('reset_hesla_zadost');
             }
             $uzivatel = $entityManager->getRepository(Uzivatel::class)->findOneBy(['email' => $email]);

             if (!$uzivatel) {
                 $this->addFlash('success', 'Pokud byl e-mail nalezen, byl na něj odeslán odkaz pro změnu hesla.');
                 return $this->redirectToRoute('app_login');
             }

             $token = bin2hex(random_bytes(32));
             $uzivatel->setResetToken($token);
             $uzivatel->setResetTokenExpiresAt((new \DateTime())->modify('+30 minutes'));

             $entityManager->flush();

             $link = $this->generateUrl('reset_hesla_nove', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

             $emailObj = (new Email())
                 ->from('tester.aukce@gmail.com')
                 ->replyTo('no-reply@aukce.test') // v googlu se ale bude vypisovat jako tester.aukce kvůli googlu a ověření SMTP
                 ->to($uzivatel->getEmail())
                 ->subject('Obnovení hesla - Aukční portál')
                 ->html($this->renderView('security/email_reset_hesla.html.twig', ['uzivatel' => $uzivatel, 'resetUrl' => $link]));

             try {
                 $mailer->send($emailObj);
             } catch (\Throwable $e) {
                 $this->addFlash('error', 'Nepodařilo se odeslat e-mail pro obnovu hesla. Zkuste to prosím znovu později.');
                 return $this->redirectToRoute('reset_hesla_zadost');
             }

             $this->addFlash('success', 'Na Váš e-mail byl odeslán odkaz pro obnovení hesla.');
             return $this->redirectToRoute('app_login');
         }

         return $this->render('security/reset_hesla_zadost.html.twig');
     }

     //  Ověří reset token a uloží nové heslo uživatele.
     #[Route('/reset-hesla/{token}', name: 'reset_hesla_nove')]
     public function nove(string $token, Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response{
         $uzivatel = $entityManager->getRepository(Uzivatel::class)->findOneBy(['reset_token' => $token]);

         if (!$uzivatel) {
             $this->addFlash('error', 'Odkaz pro reset hesla je neplatný.');
             return $this->redirectToRoute('app_login');
         }

         $ted = new \DateTime();
         if ($uzivatel->getResetTokenExpiresAt() === null || $uzivatel->getResetTokenExpiresAt() < $ted) {
             $this->addFlash('error', 'Platnost odkazu vypršela. Požádejte o nový.');
             $uzivatel->setResetToken(null);
             $uzivatel->setResetTokenExpiresAt(null);
             $entityManager->flush();

             return $this->redirectToRoute('reset_hesla_zadost');
         }

         if($request->isMethod('POST')) {
             if (!$this->isCsrfTokenValid('reset_hesla', $request->request->get('_token'))) {
                 throw $this->createAccessDeniedException('Neplatný CSRF token.');
             }

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
             if (!preg_match('/\d/', $heslo)) {
                 $this->addFlash('error', 'Heslo musí obsahovat alespoň jedno číslo.');
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