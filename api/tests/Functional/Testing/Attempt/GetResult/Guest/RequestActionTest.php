<?php

declare(strict_types=1);

namespace Tests\Functional\Testing\Attempt\GetResult\Guest;

use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\Functional\FixturesLoader;
use Tests\Functional\Json;

/**
 * @internal
 * @coversNothing
 */
final class RequestActionTest extends WebTestCase
{
    private readonly KernelBrowser $client;
    private readonly ContainerInterface $container;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();
        $this->client->disableReboot();
        $this->container = $this->client->getContainer();

        $fixturesLoader = new FixturesLoader($this->container);
        $fixturesLoader->loadFixtures([RequestFixture::class]);
    }

    public function testSuccessGuestUser(): void
    {
        $this->client->jsonRequest(
            'GET',
            '/v1/testing/attempts/' . RequestFixture::ATTEMPT_ID . '/result',
            [],
            ['HTTP_X-Guest-ID' => RequestFixture::GUEST_ID]
        );

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertArrayHasKey('id', $data);
        self::assertArrayHasKey('status', $data);
        self::assertArrayHasKey('score', $data);
        self::assertArrayHasKey('mistakes', $data);
        self::assertArrayHasKey('ticketNumber', $data);
        self::assertArrayHasKey('startedAt', $data);
        self::assertArrayHasKey('finishedAt', $data);
        self::assertArrayHasKey('test', $data);
        self::assertArrayHasKey('questions', $data);
        self::assertArrayHasKey('email', $data);
    }

    public function testInvalidGuestId(): void
    {
        $this->client->jsonRequest(
            'GET',
            '/v1/testing/attempts/' . RequestFixture::ATTEMPT_ID . '/result',
            [],
            ['HTTP_X-Guest-ID' => 'invalid']
        );

        self::assertEquals(422, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'userId' => 'This is not a valid UUID.',
        ]], $data);
    }
}
