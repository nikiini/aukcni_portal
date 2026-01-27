<?php

namespace App\Entity;

use App\Repository\AukceKategorieRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AukceKategorieRepository::class)]
class AukceKategorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'aukceKategorie')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Aukce $aukce = null;

    #[ORM\ManyToOne(inversedBy: 'aukceKategorie')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Kategorie $kategorie = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAukce(): ?Aukce
    {
        return $this->aukce;
    }

    public function setAukce(?Aukce $aukce): static
    {
        $this->aukce = $aukce;

        return $this;
    }

    public function getKategorie(): ?Kategorie
    {
        return $this->kategorie;
    }

    public function setKategorie(?Kategorie $kategorie): static
    {
        $this->kategorie = $kategorie;

        return $this;
    }
}
