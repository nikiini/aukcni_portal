<?php

namespace App\Controller;

use App\Repository\UzivatelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeWebhookController extends AbstractController
{
    //  Přijme Stripe webhook, ověří podpis a připíše kredity uživateli.
    #[Route('/stripe/webhook', name: 'stripe_webhook', methods: ['POST'])]
    public function webhook(
        Request $request,
        UzivatelRepository $uzivatelRepository,
        EntityManagerInterface $em
    ): Response {

        $payload = $request->getContent();
        $event = json_decode($payload);

        // Ověříme, že jde o úspěšnou platbu
        if ($event->type === 'checkout.session.completed') {

            $session = $event->data->object;

            $userId = $session->metadata->user_id ?? null;
            $castka = $session->amount_total ?? 0;


            if ($userId) {
                $uzivatel = $uzivatelRepository->find($userId);

                if ($uzivatel) {
                    // Stripe vrací částku v haléřích
                    $castkaVKorunach = $castka / 100;

                    $novaHodnota = (float)$uzivatel->getKredity() + $castkaVKorunach;
                    $uzivatel->setKredity(number_format($novaHodnota, 2, '.', ''));

                    $em->flush();
                }
            }
        }

        return new Response('Webhook přijat', 200);
    }
}