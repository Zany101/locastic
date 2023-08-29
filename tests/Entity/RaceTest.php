<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Race;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RacesTest extends KernelTestCase
{
    /**
     * @see Race::__construct()
     */
    public function testConstructNomicalCase(): void
    {
        $race = new Race();

        $race->setTitle('title');
        $race->setFullName('FullName');
        $race->setDistance('long');
        $race->setAgeCategory('M24-16');
        $race->setRaceDate('1-1-2000');
        $race->setTime("00:00:00");
        
        self::assertInstanceOf(Race::class, $race);
    }
   
}