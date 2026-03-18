<?php
namespace App\Command;

use App\Repository\AukceRepository;
use App\Service\VyhodnoceniAukceService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:ukoncit-aukce',
    description: 'Ukončí aukce, které už doběhly, určí vítěze a vypořádá kredity.',
)]
class UkoncitAukceCommand extends Command
{
    //  Připraví službu pro vyhodnoceníukončených aukcí.
    public function __construct(
        private AukceRepository $aukceRepository,
        private EntityManagerInterface $entityManager,
        private VyhodnoceniAukceService $vyhodnoceniAukceService
    ) {
        parent::__construct();
    }
    //  Vyhledá a vyhodnotí aukce, které již skončily.
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $ted = new \DateTime();

        $aukceKUkonceni = $this->aukceRepository->findBy([
            'stav' => 'aktivni'
        ]);

        $pocetUkoncenych = 0;

        foreach ($aukceKUkonceni as $aukce) {
            if (!$aukce->getCasKonce() || $aukce->getCasKonce() > $ted) {
                continue;
            }
            $aukce->setStav('ukoncena');
            $this->vyhodnoceniAukceService->vyhodnotitUkoncenouAukci($aukce);

            $pocetUkoncenych++;
        }

        $this->entityManager->flush();

        $output->writeln('Ukončeno aukcí: ' . $pocetUkoncenych);

        return Command::SUCCESS;
    }
}