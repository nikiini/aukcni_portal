<?php

namespace App\Entity;

use App\Repository\FotkyAukciRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FotkyAukciRepository::class)]
class FotkyAukci
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $cesta = null;

    #[ORM\ManyToOne(inversedBy: 'fotkyAukce')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Aukce $aukce = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $vytvoreno = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCesta(): ?string
    {
        return $this->cesta;
    }

    public function setCesta(string $cesta): static
    {
        $this->cesta = $cesta;

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
