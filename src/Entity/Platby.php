<?php

namespace App\Entity;

use App\Repository\PlatbyRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlatbyRepository::class)]
class Platby
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'platby')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Uzivatel $uzivatel = null;

    #[ORM\ManyToOne(inversedBy: 'platby')]
    private ?Aukce $aukce = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $castka = null;

    #[ORM\Column(length: 255)]
    private ?string $typ = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $popis = null;

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

    public function getCastka(): ?string
    {
        return $this->castka;
    }

    public function setCastka(string $castka): static
    {
        $this->castka = $castka;

        return $this;
    }

    public function getTyp(): ?string
    {
        return $this->typ;
    }

    public function setTyp(string $typ): static
    {
        $this->typ = $typ;

        return $this;
    }

    public function getPopis(): ?string
    {
        return $this->popis;
    }

    public function setPopis(?string $popis): static
    {
        $this->popis = $popis;

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
