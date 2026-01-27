<?php
namespace App\Security;
use App\Entity\Uzivatel;
use Couchbase\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class EmailNeboUsernameProvider implements UserProviderInterface{
    public function __construct(private EntityManagerInterface $spravceEntit){

    }
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
    public function refreshUser(UserInterface $uzivatel): UserInterface{
        return $uzivatel;
    }

    public function supportsClass(string $trida): bool{
        return $trida === Uzivatel::class;
    }
}


