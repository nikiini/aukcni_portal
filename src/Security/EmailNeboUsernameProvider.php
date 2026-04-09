<?php
namespace App\Security;
use App\Entity\Uzivatel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class EmailNeboUsernameProvider implements UserProviderInterface{
    //  Přijme repozitář uživatelů pro načítání identity.
    public function __construct(private EntityManagerInterface $spravceEntit){

    }
    //  Umožní přihlášení pomocí e-mailu nebo uživatelského jména.
    public function loadUserByIdentifier(string $identifikator): UserInterface{
        //podle emailu
        $uzivatel = $this->spravceEntit
            ->getRepository(Uzivatel::class)
            ->findOneBy(['email' => $identifikator]);

        //pokud ne email tak podle uzivatelskeho jmena
        if(!$uzivatel){
            $uzivatel = $this->spravceEntit
                ->getRepository(Uzivatel::class)
                ->findOneBy(['uzivatelske_jmeno' => $identifikator]);
        }
        if(!$uzivatel){
            throw new UserNotFoundException('Uživatel nebyl nalezen.');
        }

        return $uzivatel;
    }
    //  Obnoví instanci uživatele po přihlášení.
    public function refreshUser(UserInterface $uzivatel): UserInterface
    {
        if (!$uzivatel instanceof Uzivatel) {
            throw new UnsupportedUserException(sprintf('Nepodporovaný typ uživatele: %s', get_class($uzivatel)));
        }

        $novyUzivatel = $this->spravceEntit
            ->getRepository(Uzivatel::class)
            ->find($uzivatel->getId());

        if (!$novyUzivatel) {
            throw new UserNotFoundException('Uživatel už v databázi neexistuje.');
        }

        return $novyUzivatel;
    }
    //  Vrátí, zda provider pracuje s danou třídou uživatele.
    public function supportsClass(string $trida): bool
    {
        return $trida === Uzivatel::class || is_subclass_of($trida, Uzivatel::class);
    }
}


