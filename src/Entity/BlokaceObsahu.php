<?php

namespace App\Entity;

use App\Repository\BlokaceObsahuRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BlokaceObsahuRepository::class)]
class BlokaceObsahu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'blokaceObsahu')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Uzivatel $uzivatel = null;

    #[ORM\ManyToOne(inversedBy: 'blokaceObsahu')]
    private ?Aukce $aukce = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $duvod = null;

    #[ORM\Column(length: 255)]
    private ?string $stav = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $vytvoreno = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUzivatel(): ?Uzivatel
    {
        return $this->uzivatel;
    }

    public function setUzivatel(?Uzivatel $uzivatel): static
    {
        $this->uzivatel = $uzivatel;

        return $this;
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

    public function getDuvod(): ?string
    {
        return $this->duvod;
    }

    public function setDuvod(?string $duvod): static
    {
        $this->duvod = $duvod;

        return $this;
    }

    public function getStav(): ?string
    {
        return $this->stav;
    }

    public function setStav(string $stav): static
    {
        $this->stav = $stav;

        return $this;
    }

    public function getVytvoreno(): ?\DateTimeInterface
    {
        return $this->vytvoreno;
    }

    public function setVytvoreno(\DateTimeInterface $vytvoreno): static
    {
        $this->vytvoreno = $vytvoreno;

        return $this;
    }
}
