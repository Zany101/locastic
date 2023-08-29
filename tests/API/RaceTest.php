<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
#yest
class RaceTest extends ApiTestCase
{
    public function testGetCollectionRaces(): void
    {
        $response = static::createClient()->request('GET', '/api/races.jsonld');

        $this->assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            "@context" => "/api/contexts/Races",
            "@id" => "/api/races",
            "@type" => "hydra:Collection",
            "hydra:totalItems" => 0,
        ]);
    }

    public function testGetParticipants(): void
    {
        $response = static::createClient()->request('GET', '/api/races/1');

        $this->assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            "@context" => "/api/contexts/Participants",
            "@id" => "/api/races/1",
            "@type" => "hydra:Collection",
            "hydra:totalItems" => 0,
        ]);
    }
 
}
