<?php

declare(strict_types=1);

namespace Tests\Functional\Parser\GetParser;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\Functional\FixturesLoader;
use Tests\Functional\Json;

final class RequestActionTest extends WebTestCase
{
    private KernelBrowser $client;
    public function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();

        $container = $this->client->getContainer();
        $fixtureLoader = new FixturesLoader($container);
        $fixtureLoader->loadFixtures([RequestFixture::class]);
    }

    public function testSuccess(): void
    {
        $this->client->request('GET', '/v1/parser/'. RequestFixture::PARSER_ID);

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals('https://example.com', $data['host']);
        self::assertEquals('some sookie', $data['cookie']);
    }
    public function testNotFound(): void
    {
        $this->client->jsonRequest('GET', '/v1/parser/'. RequestFixture::PARSER_NOT_FOUND_ID);
        self::assertEquals(409, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['message' => 'Parser not found.'], $data);
    }

}
