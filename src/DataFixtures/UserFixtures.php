<?php

namespace App\DataFixtures;

use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function __construct()
    {
    }

    public function load(ObjectManager $manager)
    {
        UserFactory::createMany(10);
        UserFactory::createOne(
            [
                'email' => 'admin@local',
                'roles' => ['ROLE_ADMIN'],
            ]
        );
    }
}
