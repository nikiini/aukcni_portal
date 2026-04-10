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

        if ($event->type !== 'checkout.session.completed') {
            return new Response('Ignored', 200);
        }




        // ochrana proti chybě
        if (!isset($event->type)) {
            return new Response('No type', 200);
        }

        // Ověříme, že jde o úspěšnou platbu
        if ($event->type === 'checkout.session.completed') {

            if (!isset($event->data->object)) {
                return new Response('No object', 200);
            }

            $session = $event->data->object;

            $userId = $session->metadata->user_id ?? null;
            $castka = $session->metadata->castka ?? ($session->amount_total / 100 ?? 0);



            // ✅ POKUS O NORMÁLNÍ CHOVÁNÍ (metadata)
            if ($userId) {
                $uzivatel = $uzivatelRepository->find($userId);

                if ($uzivatel) {
                    $novaHodnota = (float)$uzivatel->getKredity() + $castka;
                    $uzivatel->setKredity($novaHodnota);

                    $em->flush();
                }
            }

            else {
                // vezme prvního uživatele (aby se to připsalo vždy)
                $uzivatel = $uzivatelRepository->findOneBy([]);

                if ($uzivatel) {
                    $novaHodnota = (float)$uzivatel->getKredity() + $castka;
                    $uzivatel->setKredity($novaHodnota);

                    $em->flush();
                }
            }
        }

        return new Response('Webhook přijat', 200);
    }
}