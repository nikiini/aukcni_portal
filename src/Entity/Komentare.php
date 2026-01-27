<?php

namespace App\Entity;

use App\Repository\KomentareRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: KomentareRepository::class)]
class Komentare
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'komentare')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Aukce $aukce = null;

    #[ORM\ManyToOne(inversedBy: 'komentare')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Uzivatel $uzivatel = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $text = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $hodnoceni = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $vytvoreno = null;

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

    public function getHodnoceni(): ?int
    {
        return $this->hodnoceni;
    }

    public function setHodnoceni(?int $hodnoceni): static
    {
        $this->hodnoceni = $hodnoceni;

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
