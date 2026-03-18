<?php

namespace App\Entity;

use App\Repository\ReportAukceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReportAukceRepository::class)]
#[ORM\Table(
    name: 'report_aukce',
    uniqueConstraints: [
        new ORM\UniqueConstraint(
            name: 'report_unikatni',
            columns: ['aukce_id', 'nahlasujici_id']
        )
    ]
)]
class ReportAukce
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $duvod = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $vytvoreno = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Aukce $aukce = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Uzivatel $nahlasujici = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Uzivatel $nahlaseny = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDuvod(): ?string
    {
        return $this->duvod;
    }

    public function setDuvod(string $duvod): static
    {
        $this->duvod = $duvod;

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

    public function getAukce(): ?Aukce
    {
        return $this->aukce;
    }

    public function setAukce(?Aukce $aukce): static
    {
        $this->aukce = $aukce;

        return $this;
    }

    public function getNahlasujici(): ?Uzivatel
    {
        return $this->nahlasujici;
    }

    public function setNahlasujici(?Uzivatel $nahlasujici): static
    {
        $this->nahlasujici = $nahlasujici;

        return $this;
    }

    public function getNahlaseny(): ?Uzivatel
    {
        return $this->nahlaseny;
    }

    public function setNahlaseny(?Uzivatel $nahlaseny): static
    {
        $this->nahlaseny = $nahlaseny;

        return $this;
    }
}
