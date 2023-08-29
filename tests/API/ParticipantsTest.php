<?php

namespace App\Tests;

use App\Entity\Participants;
use App\Factory\ParticipantsFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Contracts\Service\ServiceProviderInterface;
use ApiPlatform\Symfony\Bundle\Test\Client;
use ApiPlatform\Symfony\Routing\Router;

class ParticipantsTest extends ApiTestCase
{
    use RefreshDatabaseTrait;
    use ResetDatabase;
    use Factories;

    private Client $client;

    private Router $router;

    protected function setup(): void
    {
        $this->client = static::createClient();
        $router = static::getContainer()->get('api_platform.router');
        if (!$router instanceof Router) {
            throw new \RuntimeException('api_platform.router service not found.');
        }

        $this->router = $router;
    }

    public function testUpdateParticipant(): void
    {
        ParticipantsFactory::createOne();

        $participants = static::getContainer()->get('doctrine')->getRepository(Participants::class)->findOneBy();
        self::assertInstanceOf(Participants::class, $participants);
        if (!$participants instanceof Participants) {
            throw new \LogicException('Participants not found.');
        }

        $this->client->request('PUT', $this->router->generate('_api_/participantss/{id}/generate-cover{._format}_put', ['id' => $participants->getId()]), [
            'json' => [],
        ]);

        $messengerReceiverLocator = static::getContainer()->get('messenger.receiver_locator');
        if (!$messengerReceiverLocator instanceof ServiceProviderInterface) {
            throw new \RuntimeException('messenger.receiver_locator service not found.');
        }

        self::assertResponseIsSuccessful();
        self::assertSame(
            1,
            $messengerReceiverLocator->get('doctrine')->getMessageCount(),
            'No message has been sent.'
        );
    }
}
