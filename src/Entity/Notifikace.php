<?php

namespace App\Entity;

use App\Repository\NotifikaceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotifikaceRepository::class)]
class Notifikace
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'notifikace')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Uzivatel $uzivatel = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $text = null;

    #[ORM\Column(length: 255)]
    private ?string $typ = null;

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

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

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
