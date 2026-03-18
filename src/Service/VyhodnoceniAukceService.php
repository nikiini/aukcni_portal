<?php

namespace App\Service;

use App\Entity\Aukce;
use App\Entity\Notifikace;
use App\Repository\SazkyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class VyhodnoceniAukceService
{
    //  Připraví závislosti potřebné pro automatické vyhodnocení ukončených aukcí.
    public function __construct(
        private SazkyRepository $sazkyRepository,
        private EntityManagerInterface $entityManager,
        private MailerInterface $mailer,
        private Environment $twig
    ) {
    }
    //  Vybere vítěze ukončené aukce a vytvoří související notifikace.
    public function vyhodnotitUkoncenouAukci(Aukce $aukce): void
    {
        if ($aukce->getStav() !== 'ukoncena') {
            $aukce->setStav('ukoncena');
        }

        $nejvyssiSazka = $this->sazkyRepository->findOneBy(
            ['aukce' => $aukce],
            ['castka' => 'DESC', 'vytvoreno' => 'ASC']
        );

        if ($nejvyssiSazka) {
            $vitez = $nejvyssiSazka->getUzivatel();
            $aukce->setVitez($vitez);

            if (!$aukce->isVyuctovana()) {
                $autor = $aukce->getUzivatel();

                if ($autor) {
                    $autorKredity = (float) $autor->getKredity();
                    $autorKredity += (float) $nejvyssiSazka->getCastka();
                    $autor->setKredity(number_format($autorKredity, 2, '.', ''));
                }

                $aukce->setVyuctovana(true);
            }

            if (!$this->existujeNotifikace($vitez, 'vyhra', $aukce->getNazev())) {
                $notifikaceVyhra = new Notifikace();
                $notifikaceVyhra->setUzivatel($vitez);
                $notifikaceVyhra->setTyp('vyhra');
                $notifikaceVyhra->setStav('nova');
                $notifikaceVyhra->setText(
                    'Vyhrál(a) jste aukci|AUKCE|' . $aukce->getNazev() . '|' . $aukce->getVerejneId()
                );
                $notifikaceVyhra->setVytvoreno(new \DateTime());
                $this->entityManager->persist($notifikaceVyhra);
            }

            if ($vitez->getEmail()) {
                $email = (new Email())
                    ->to($vitez->getEmail())
                    ->subject('Vyhrál jste aukci')
                    ->text(
                        'Gratulujeme! Vyhrál(a) jste aukci "' . $aukce->getNazev() . '".'
                    );
                try {
                    $this->mailer->send($email);
                } catch (\Throwable $chyba) {
                    // pokud email selže, aukce se normálně ukončí
                }
            }
            $this->entityManager->flush();

        }

        $autor = $aukce->getUzivatel();
        if ($autor && !$this->existujeNotifikace($autor, 'ukonceni', $aukce->getNazev())) {
            $notifikaceAutor = new Notifikace();
            $notifikaceAutor->setUzivatel($autor);
            $notifikaceAutor->setStav('nova');
            $notifikaceAutor->setTyp('ukonceni');
            $notifikaceAutor->setText('Vaše aukce "' . $aukce->getNazev() . '" byla ukončena.');
            $notifikaceAutor->setVytvoreno(new \DateTime());
            $this->entityManager->persist($notifikaceAutor);
        }
    }
    //  Ověří, zda už podobná notifikace nebyla vytvořena dříve.
    private function existujeNotifikace($uzivatel, string $typ, string $nazevAukce): bool
    {
        $repozitarNotifikaci = $this->entityManager->getRepository(Notifikace::class);

        $notifikace = $repozitarNotifikaci->findOneBy([
            'uzivatel' => $uzivatel,
            'typ' => $typ,
        ]);

        if (!$notifikace) {
            return false;
        }

        return str_contains($notifikace->getText(), $nazevAukce);
    }
}