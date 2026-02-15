<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $rootUser = new User();
        $rootUser->setLogin('root')
            ->setPhone('00000000')
            ->setPass('rootpass');
        $manager->persist($rootUser);

        for ($i = 1; $i <= 9; $i++) {
            $user = new User();
            $user->setLogin("user{$i}")
                ->setPhone("099" . str_pad((string)$i, 5, '0', STR_PAD_LEFT))
                ->setPass("pass{$i}");
            $manager->persist($user);
        }

        $manager->flush();
    }
}

