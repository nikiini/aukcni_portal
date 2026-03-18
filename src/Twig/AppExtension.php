<?php

namespace App\Twig;

use App\Repository\KategorieRepository;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class AppExtension extends AbstractExtension implements GlobalsInterface
{
    private $kategorieRepository;
    //  Uloží potřebné hodnoty pro sdílení v Twig šablonách.
    public function __construct(KategorieRepository $kategorieRepository)
    {
        $this->kategorieRepository = $kategorieRepository;
    }
    //  Vrátí globální proměnné dostupné ve všech šablonách.
    public function getGlobals(): array
    {
        return [
            'kategorieNavigace' => $this->kategorieRepository->najdiVseAbecedne()
        ];
    }
}