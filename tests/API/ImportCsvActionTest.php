<?php

namespace App\Tests;

use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use ApiPlatform\Symfony\Bundle\Test\Client;
use ApiPlatform\Symfony\Routing\Router;
use App\Dto\ImportCsv;

class ImportCsvActionTest extends ApiTestCase
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

    public function testCsvImport(): void
    {
        $file = new UploadedFile('fixtures/files/file.csv', 'file.csv', 'csv');
        $client = self::createClient();
        $client->request('POST', '/api/races', [
            'headers' => ['Content-Type' => 'multipart/form-data'],
            'extra' => [
                'parameters' => [
                    'title' => 'My file uploaded',
                    'date' => '2023-10-18',
                ],
                'files' => [
                    'upload' => $file,
                ],
            ]
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(ImportCsv::class);
        $this->assertJsonContains([
            'title' => 'My file uploaded',
            'date' => '2023-10-18',
        ]);
    }
}
