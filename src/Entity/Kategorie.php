<?php

namespace App\Entity;

use App\Repository\KategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: KategorieRepository::class)]
class Kategorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nazev = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $popis = null;

    /**
     * @var Collection<int, AukceKategorie>
     */
    #[ORM\OneToMany(targetEntity: AukceKategorie::class, mappedBy: 'kategorie')]
    private Collection $aukceKategorie;

    public function __construct()
    {
        $this->aukceKategorie = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNazev(): ?string
    {
        return $this->nazev;
    }

    public function setNazev(string $nazev): static
    {
        $this->nazev = $nazev;

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

    /**
     * @return Collection<int, AukceKategorie>
     */
    public function getAukceKategorie(): Collection
    {
        return $this->aukceKategorie;
    }

    public function addAukceKategorie(AukceKategorie $aukceKategorie): static
    {
        if (!$this->aukceKategorie->contains($aukceKategorie)) {
            $this->aukceKategorie->add($aukceKategorie);
            $aukceKategorie->setKategorie($this);
        }

        return $this;
    }

    public function removeAukceKategorie(AukceKategorie $aukceKategorie): static
    {
        if ($this->aukceKategorie->removeElement($aukceKategorie)) {
            // set the owning side to null (unless already changed)
            if ($aukceKategorie->getKategorie() === $this) {
                $aukceKategorie->setKategorie(null);
            }
        }

        return $this;
    }
}
