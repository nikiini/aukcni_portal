<?php
namespace app\fixtures;

use App\Entity\Uzivatel;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UzivatelFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
        $uzivatel = new Uzivatel();
        $uzivatel->setEmail('test@test.com');
        $uzivatel->setCeleJmeno('Test uzivatel');
        $uzivatel->setRoles('ROLE_USER');


    }
}