<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UsersFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user = new \App\Entity\User();
        $user->setPassword('test');
        $user->setRoles('rol1');
        $user->setUsername('username1');
        $manager->persist($user);

        $user = new \App\Entity\User();
        $user->setPassword('test');
        $user->setRoles('rol2');
        $user->setUsername('username2');
        $manager->persist($user);
        
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
