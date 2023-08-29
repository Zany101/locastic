<?php

namespace App\DataFixtures;

use App\Factory\RaceFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $races = RaceFactory::createMany(10000);

    }
}
