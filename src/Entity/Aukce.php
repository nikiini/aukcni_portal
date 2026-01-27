<?php

namespace App\Entity;

use App\Repository\AukceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AukceRepository::class)]
class Aukce
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nazev = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $popis = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $vychozi_cena = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $aktualni_cena = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $cas_zacatku = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $cas_konce = null;

    #[ORM\Column(length: 255)]
    private ?string $stav = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $hlavni_foto = null;

    #[ORM\ManyToOne(inversedBy: 'aukce')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Uzivatel $uzivatel = null;

    /**
     * @var Collection<int, FotkyAukci>
     */
    #[ORM\OneToMany(targetEntity: FotkyAukci::class, mappedBy: 'aukce')]
    private Collection $fotkyAukce;

    /**
     * @var Collection<int, Sazky>
     */
    #[ORM\OneToMany(targetEntity: Sazky::class, mappedBy: 'aukce', orphanRemoval: true)]
    private Collection $sazky;

    /**
     * @var Collection<int, AukceKategorie>
     */
    #[ORM\OneToMany(targetEntity: AukceKategorie::class, mappedBy: 'aukce')]
    private Collection $aukceKategorie;

    /**
     * @var Collection<int, Komentare>
     */
    #[ORM\OneToMany(targetEntity: Komentare::class, mappedBy: 'aukce', orphanRemoval: true)]
    private Collection $komentare;

    /**
     * @var Collection<int, Platby>
     */
    #[ORM\OneToMany(targetEntity: Platby::class, mappedBy: 'aukce', orphanRemoval: true)]
    private Collection $platby;

    /**
     * @var Collection<int, LogyAukce>
     */
    #[ORM\OneToMany(targetEntity: LogyAukce::class, mappedBy: 'aukce', orphanRemoval: true)]
    private Collection $logyAukce;

    /**
     * @var Collection<int, BlokaceObsahu>
     */
    #[ORM\OneToMany(targetEntity: BlokaceObsahu::class, mappedBy: 'aukce', orphanRemoval: true)]
    private Collection $blokaceObsahu;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Uzivatel $vitez = null;

    public function getVitez(): ?Uzivatel{
        return $this->vitez;
    }
    public function setVitez(?Uzivatel $vitez): static{
        $this->vitez = $vitez;
        return $this;
    }

    public function __construct()
    {
        $this->fotkyAukce = new ArrayCollection();
        $this->sazky = new ArrayCollection();
        $this->aukceKategorie = new ArrayCollection();
        $this->komentare = new ArrayCollection();
        $this->platby = new ArrayCollection();
        $this->logyAukce = new ArrayCollection();
        $this->blokaceObsahu = new ArrayCollection();
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

    public function getVychoziCena(): ?string
    {
        return $this->vychozi_cena;
    }

    public function setVychoziCena(string $vychozi_cena): static
    {
        $this->vychozi_cena = $vychozi_cena;

        return $this;
    }

    public function getAktualniCena(): ?string
    {
        return $this->aktualni_cena;
    }

    public function setAktualniCena(?string $aktualni_cena): static
    {
        $this->aktualni_cena = $aktualni_cena;

        return $this;
    }

    public function getCasZacatku(): ?\DateTimeInterface
    {
        return $this->cas_zacatku;
    }

    public function setCasZacatku(\DateTimeInterface $cas_zacatku): static
    {
        $this->cas_zacatku = $cas_zacatku;

        return $this;
    }

    public function getCasKonce(): ?\DateTimeInterface
    {
        return $this->cas_konce;
    }

    public function setCasKonce(\DateTimeInterface $cas_konce): static
    {
        $this->cas_konce = $cas_konce;

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

    public function getHlavniFoto(): ?string
    {
        return $this->hlavni_foto;
    }

    public function setHlavniFoto(?string $hlavni_foto): static
    {
        $this->hlavni_foto = $hlavni_foto;

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

    /**
     * @return Collection<int, FotkyAukci>
     */
    public function getFotkyAukce(): Collection
    {
        return $this->fotkyAukce;
    }

    public function addFotkyAukce(FotkyAukci $fotkyAukce): static
    {
        if (!$this->fotkyAukce->contains($fotkyAukce)) {
            $this->fotkyAukce->add($fotkyAukce);
            $fotkyAukce->setAukce($this);
        }

        return $this;
    }

    public function removeFotkyAukce(FotkyAukci $fotkyAukce): static
    {
        if ($this->fotkyAukce->removeElement($fotkyAukce)) {
            // set the owning side to null (unless already changed)
            if ($fotkyAukce->getAukce() === $this) {
                $fotkyAukce->setAukce(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Sazky>
     */
    public function getSazky(): Collection
    {
        return $this->sazky;
    }

    public function addSazky(Sazky $sazky): static
    {
        if (!$this->sazky->contains($sazky)) {
            $this->sazky->add($sazky);
            $sazky->setAukce($this);
        }

        return $this;
    }

    public function removeSazky(Sazky $sazky): static
    {
        if ($this->sazky->removeElement($sazky)) {
            // set the owning side to null (unless already changed)
            if ($sazky->getAukce() === $this) {
                $sazky->setAukce(null);
            }
        }

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
            $aukceKategorie->setAukce($this);
        }

        return $this;
    }

    public function removeAukceKategorie(AukceKategorie $aukceKategorie): static
    {
        if ($this->aukceKategorie->removeElement($aukceKategorie)) {
            // set the owning side to null (unless already changed)
            if ($aukceKategorie->getAukce() === $this) {
                $aukceKategorie->setAukce(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Komentare>
     */
    public function getKomentare(): Collection
    {
        return $this->komentare;
    }

    public function addKomentare(Komentare $komentare): static
    {
        if (!$this->komentare->contains($komentare)) {
            $this->komentare->add($komentare);
            $komentare->setAukce($this);
        }

        return $this;
    }

    public function removeKomentare(Komentare $komentare): static
    {
        if ($this->komentare->removeElement($komentare)) {
            // set the owning side to null (unless already changed)
            if ($komentare->getAukce() === $this) {
                $komentare->setAukce(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Platby>
     */
    public function getPlatby(): Collection
    {
        return $this->platby;
    }

    public function addPlatby(Platby $platby): static
    {
        if (!$this->platby->contains($platby)) {
            $this->platby->add($platby);
            $platby->setAukce($this);
        }

        return $this;
    }

    public function removePlatby(Platby $platby): static
    {
        if ($this->platby->removeElement($platby)) {
            // set the owning side to null (unless already changed)
            if ($platby->getAukce() === $this) {
                $platby->setAukce(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, LogyAukce>
     */
    public function getLogyAukce(): Collection
    {
        return $this->logyAukce;
    }

    public function addLogyAukce(LogyAukce $logyAukce): static
    {
        if (!$this->logyAukce->contains($logyAukce)) {
            $this->logyAukce->add($logyAukce);
            $logyAukce->setAukce($this);
        }

        return $this;
    }

    public function removeLogyAukce(LogyAukce $logyAukce): static
    {
        if ($this->logyAukce->removeElement($logyAukce)) {
            // set the owning side to null (unless already changed)
            if ($logyAukce->getAukce() === $this) {
                $logyAukce->setAukce(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BlokaceObsahu>
     */
    public function getBlokaceObsahu(): Collection
    {
        return $this->blokaceObsahu;
    }

    public function addBlokaceObsahu(BlokaceObsahu $blokaceObsahu): static
    {
        if (!$this->blokaceObsahu->contains($blokaceObsahu)) {
            $this->blokaceObsahu->add($blokaceObsahu);
            $blokaceObsahu->setAukce($this);
        }

        return $this;
    }

    public function removeBlokaceObsahu(BlokaceObsahu $blokaceObsahu): static
    {
        if ($this->blokaceObsahu->removeElement($blokaceObsahu)) {
            // set the owning side to null (unless already changed)
            if ($blokaceObsahu->getAukce() === $this) {
                $blokaceObsahu->setAukce(null);
            }
        }

        return $this;
    }
}
