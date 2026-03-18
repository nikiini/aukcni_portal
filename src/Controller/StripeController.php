<?php
namespace App\Controller;

use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
//  Řadič obsluhující platební styk prostřednictvím externí brány Stripe.
//  Generuje požadavky na platbu a zpracovává zpětná volání (webhooky) pro automatické připisování kreditů.
class StripeController extends AbstractController
{
    //  Vytvoří Stripe Checkout session pro dobití kreditů o pevně danou částku.
    #[Route('/dobit/{castka}', name: 'stripe_dobit')]
    public function dobit(int $castka): Response {
        //  Vytvoří Stripe Check platební relaci pro předem definovanou částku a přesměruje uživatele na zabezpečenou platební bránu.
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($this->getUser()->isBlokovan()) {
            throw $this->createAccessDeniedException('Účet je zablokován.');
        }
        if($castka <= 0) {
            throw $this->createNotFoundException();
        }

        Stripe::setApiKey($this->getParameter('stripe_secret_key'));
        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'czk',
                    'product_data' => [
                        'name' => 'Dobití kreditů',
                    ],
                    'unit_amount' => $castka * 100,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',

            'metadata' => [
                'user_id' => $this->getUser()->getId(),
            ],

            'success_url' => $this->generateUrl('stripe_success', ['castka' => $castka], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('profil', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
        return $this->redirect($session->url);
    }
    //  Zobrazí návratovou stránku po úspěšné platbě.
    #[Route('/stripe/success/{castka}', name: 'stripe_success')]
    public function success(): Response {
        $this->addFlash('success', 'Platba proběhla úspěšně. Kredit bude připsán během několika sekund.');
        return $this->redirectToRoute('profil');
    }
    //  Zpracuje vlastní částku a přesměruje na Stripe Checkout.
    #[Route('/dobit-vlastni', name: 'stripe_vlastni_castka')]
    public function vlastniCastka(): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->render('stripe/vlastni_castka.html.twig');
    }
    #[Route('/dobit-vlastni-submit', name: 'stripe_vlastni_castka_submit', methods: ['POST'])]
    public function vlastniCastkaSubmit(Request $request): Response {
        $castka = (int)$request->request->get('castka');
        if($castka < 100) {
            $this->addFlash('error', 'Minimální částka je 100Kč.');
            return $this->redirectToRoute('stripe_vlastni_castka');
        }
        return $this->redirectToRoute('stripe_dobit',['castka'=>$castka]);
    }

}
