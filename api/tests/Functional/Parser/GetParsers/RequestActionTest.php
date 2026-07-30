<?php

declare(strict_types=1);

namespace Tests\Functional\Parser\GetParsers;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\Functional\FixturesLoader;
use Tests\Functional\Json;
use Tests\Functional\Parser\GetParser\RequestFixture;

/**
 * @internal
 * @coversNothing
 */
final class RequestActionTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();

        $container = $this->client->getContainer();
        $fixtureLoader = new FixturesLoader($container);
        $fixtureLoader->loadFixtures([RequestFixture::class]);
    }

    public function testSuccess(): void
    {
        $this->client->request('GET', '/v1/parsers');

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals([
            [
                'id' => '5134bc29-ef64-414f-a0d4-b1cf0166c7e2',
                'host' => 'https://example.com',
            ],
        ], $data);
    }
}
