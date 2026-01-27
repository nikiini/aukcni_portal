<?php

namespace App\Entity;

use App\Repository\UzivatelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UzivatelRepository::class)]
class Uzivatel implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $uzivatelske_jmeno = null;

    #[ORM\Column(length: 255)]
    private ?string $cele_jmeno = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $heslo = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $reset_token = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $reset_token_expires_at = null;

    #[ORM\Column(length: 255)]
    private ?string $role = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $kredity = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profil_foto = null;

    #[ORM\Column]
    private ?bool $email_overeno = null;

    #[ORM\Column]
    private ?bool $blokovan = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $vytvoreno = null;

    #[ORM\OneToMany(targetEntity: Aukce::class, mappedBy: 'uzivatel')]
    private Collection $aukce;

    #[ORM\OneToMany(targetEntity: Sazky::class, mappedBy: 'uzivatel')]
    private Collection $sazky;

    #[ORM\OneToMany(targetEntity: Komentare::class, mappedBy: 'uzivatel')]
    private Collection $komentare;

    #[ORM\OneToMany(targetEntity: Platby::class, mappedBy: 'uzivatel')]
    private Collection $platby;

    #[ORM\OneToMany(targetEntity: LogyAukce::class, mappedBy: 'uzivatel')]
    private Collection $logyAukce;

    #[ORM\OneToMany(targetEntity: Notifikace::class, mappedBy: 'uzivatel')]
    private Collection $notifikace;

    #[ORM\OneToMany(targetEntity: Bonusy::class, mappedBy: 'uzivatel')]
    private Collection $bonusy;

    #[ORM\OneToMany(targetEntity: BlokaceObsahu::class, mappedBy: 'uzivatel')]
    private Collection $blokaceObsahu;

    #[ORM\OneToMany(targetEntity: HistorieKreditu::class, mappedBy: 'uzivatel')]
    private Collection $historieKreditu;

    public function __construct()
    {
        $this->aukce = new ArrayCollection();
        $this->sazky = new ArrayCollection();
        $this->komentare = new ArrayCollection();
        $this->platby = new ArrayCollection();
        $this->logyAukce = new ArrayCollection();
        $this->notifikace = new ArrayCollection();
        $this->bonusy = new ArrayCollection();
        $this->blokaceObsahu = new ArrayCollection();
        $this->historieKreditu = new ArrayCollection();

        $this->vytvoreno = new \DateTime();
    }

    //Security
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
            if ($this->role === 'admin') {
                return ['ROLE_ADMIN', 'ROLE_USER'];
            }
            return ['ROLE_USER'];
    }


    public function getPassword(): ?string
    {
        return $this->heslo;
    }

    public function eraseCredentials(): void
    {
        // neukládáme nic citlivého mimo hesla
    }

    //getter / setter

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUzivatelskeJmeno(): ?string
    {
        return $this->uzivatelske_jmeno;
    }

    public function setUzivatelskeJmeno(string $uzivatelske_jmeno): static
    {
        $this->uzivatelske_jmeno = $uzivatelske_jmeno;
        return $this;
    }

    public function getCeleJmeno(): ?string
    {
        return $this->cele_jmeno;
    }

    public function setCeleJmeno(string $cele_jmeno): static
    {
        $this->cele_jmeno = $cele_jmeno;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getHeslo(): ?string
    {
        return $this->heslo;
    }

    public function setHeslo(string $heslo): static
    {
        $this->heslo = $heslo;
        return $this;
    }

    public function getRole(): ?string
    {
       return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;
        return $this;
    }

    public function getKredity(): ?string
    {
        return $this->kredity;
    }

    public function setKredity(string $kredity): static
    {
        $this->kredity = $kredity;
        return $this;
    }

    public function getProfilFoto(): ?string
    {
        return $this->profil_foto;
    }

    public function setProfilFoto(?string $profil_foto): static
    {
        $this->profil_foto = $profil_foto;
        return $this;
    }

    public function isEmailOvereno(): ?bool
    {
        return $this->email_overeno;
    }

    public function setEmailOvereno(bool $email_overeno): static
    {
        $this->email_overeno = $email_overeno;
        return $this;
    }

    public function isBlokovan(): ?bool
    {
        return $this->blokovan;
    }

    public function setBlokovan(bool $blokovan): static
    {
        $this->blokovan = $blokovan;
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
    //vztahy
    public function getAukce(): Collection
    {
        return $this->aukce;
    }

    public function addAukce(Aukce $aukce): static
    {
        if (!$this->aukce->contains($aukce)) {
            $this->aukce->add($aukce);
            $aukce->setUzivatel($this);
        }
        return $this;
    }

    public function removeAukce(Aukce $aukce): static
    {
        if ($this->aukce->removeElement($aukce)) {
            if ($aukce->getUzivatel() === $this) {
                $aukce->setUzivatel(null);
            }
        }
        return $this;
    }

    public function getSazky(): Collection
    {
        return $this->sazky;
    }

    public function addSazky(Sazky $sazky): static
    {
        if (!$this->sazky->contains($sazky)) {
            $this->sazky->add($sazky);
            $sazky->setUzivatel($this);
        }
        return $this;
    }

    public function removeSazky(Sazky $sazky): static
    {
        if ($this->sazky->removeElement($sazky)) {
            if ($sazky->getUzivatel() === $this) {
                $sazky->setUzivatel(null);
            }
        }
        return $this;
    }

    public function getKomentare(): Collection
    {
        return $this->komentare;
    }

    public function addKomentare(Komentare $komentare): static
    {
        if (!$this->komentare->contains($komentare)) {
            $this->komentare->add($komentare);
            $komentare->setUzivatel($this);
        }
        return $this;
    }

    public function removeKomentare(Komentare $komentare): static
    {
        if ($this->komentare->removeElement($komentare)) {
            if ($komentare->getUzivatel() === $this) {
                $komentare->setUzivatel(null);
            }
        }
        return $this;
    }

    public function getPlatby(): Collection
    {
        return $this->platby;
    }

    public function addPlatby(Platby $platby): static
    {
        if (!$this->platby->contains($platby)) {
            $this->platby->add($platby);
            $platby->setUzivatel($this);
        }
        return $this;
    }

    public function removePlatby(Platby $platby): static
    {
        if ($this->platby->removeElement($platby)) {
            if ($platby->getUzivatel() === $this) {
                $platby->setUzivatel(null);
            }
        }
        return $this;
    }

    public function getLogyAukce(): Collection
    {
        return $this->logyAukce;
    }

    public function addLogyAukce(LogyAukce $logyAukce): static
    {
        if (!$this->logyAukce->contains($logyAukce)) {
            $this->logyAukce->add($logyAukce);
            $logyAukce->setUzivatel($this);
        }
        return $this;
    }

    public function removeLogyAukce(LogyAukce $logyAukce): static
    {
        if ($this->logyAukce->removeElement($logyAukce)) {
            if ($logyAukce->getUzivatel() === $this) {
                $logyAukce->setUzivatel(null);
            }
        }
        return $this;
    }

    public function getNotifikace(): Collection
    {
        return $this->notifikace;
    }

    public function addNotifikace(Notifikace $notifikace): static
    {
        if (!$this->notifikace->contains($notifikace)) {
            $this->notifikace->add($notifikace);
            $notifikace->setUzivatel($this);
        }
        return $this;
    }

    public function removeNotifikace(Notifikace $notifikace): static
    {
        if ($this->notifikace->removeElement($notifikace)) {
            if ($notifikace->getUzivatel() === $this) {
                $notifikace->setUzivatel(null);
            }
        }
        return $this;
    }

    public function getBonusy(): Collection
    {
        return $this->bonusy;
    }

    public function addBonusy(Bonusy $bonusy): static
    {
        if (!$this->bonusy->contains($bonusy)) {
            $this->bonusy->add($bonusy);
            $bonusy->setUzivatel($this);
        }
        return $this;
    }

    public function removeBonusy(Bonusy $bonusy): static
    {
        if ($this->bonusy->removeElement($bonusy)) {
            if ($bonusy->getUzivatel() === $this) {
                $bonusy->setUzivatel(null);
            }
        }
        return $this;
    }

    public function getBlokaceObsahu(): Collection
    {
        return $this->blokaceObsahu;
    }

    public function addBlokaceObsahu(BlokaceObsahu $blokaceObsahu): static
    {
        if (!$this->blokaceObsahu->contains($blokaceObsahu)) {
            $this->blokaceObsahu->add($blokaceObsahu);
            $blokaceObsahu->setUzivatel($this);
        }
        return $this;
    }

    public function removeBlokaceObsahu(BlokaceObsahu $blokaceObsahu): static
    {
        if ($this->blokaceObsahu->removeElement($blokaceObsahu)) {
            if ($blokaceObsahu->getUzivatel() === $this) {
                $blokaceObsahu->setUzivatel(null);
            }
        }
        return $this;
    }

    public function getHistorieKreditu(): Collection
    {
        return $this->historieKreditu;
    }

    public function addHistorieKreditu(HistorieKreditu $historieKreditu): static
    {
        if (!$this->historieKreditu->contains($historieKreditu)) {
            $this->historieKreditu->add($historieKreditu);
            $historieKreditu->setUzivatel($this);
        }
        return $this;
    }

    public function removeHistorieKreditu(HistorieKreditu $historieKreditu): static
    {
        if ($this->historieKreditu->removeElement($historieKreditu)) {
            if ($historieKreditu->getUzivatel() === $this) {
                $historieKreditu->setUzivatel(null);
            }
        }
        return $this;
    }
    public function getResetToken(): ?string
    {
        return $this->reset_token;
    }
    public function setResetToken(?string $reset_token): static{
        $this->reset_token = $reset_token;
        return $this;
    }
    public function getResetTokenExpiresAt(): ?\DateTimeInterface{
        return $this->reset_token_expires_at;
    }
    public function setResetTokenExpiresAt(?\DateTimeInterface $reset_token_expires_at): static{
        $this->reset_token_expires_at = $reset_token_expires_at;
        return $this;
    }

}
