<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\RaceMaster;
use DateTimeImmutable;

class AppFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        $racemaster = new RaceMaster();
        $racemaster->setRaceTitle('Marathon 2');
        $racemaster->setRaceDate(new DateTimeImmutable('2023-02-18'));
        $manager->persist($racemaster);
        $manager->flush();
    }
}
