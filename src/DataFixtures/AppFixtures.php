<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();

        $user
            ->setFirstName('Prabudh')
            ->setLastName('Mishra')
            ->setEmail('prabudh.mishra@gmail.com')
            ->setPassword($this->passwordHasher->hashPassword($user, 'admin@123'));

        $manager->persist($user);
        $manager->flush();
    }
}
