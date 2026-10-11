<?php

declare(strict_types=1);

namespace Tests\Functional\Testing\Attempt\Launch\Guest;

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

    public function testSuccess(): void
    {
        $this->client->jsonRequest(
            'POST',
            '/v1/testing/attempts',
            [
                'testId' => RequestFixture::TEST_ID,
                'ticketNumber' => RequestFixture::TICKET_NUMBER,
            ],
            [
                'HTTP_X-Guest-ID' => RequestFixture::GUEST_ID,
            ]
        );

        self::assertEquals(201, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertArrayHasKey('attemptId', $data);
    }

    public function testTooMany(): void
    {
        $this->client->jsonRequest(
            'POST',
            '/v1/testing/attempts',
            [
                'testId' => RequestFixture::TEST_ID,
                'ticketNumber' => RequestFixture::TICKET_NUMBER,
            ],
            [
                'HTTP_X-Guest-ID' => RequestFixture::GUEST_ID_ATTEMPT_EXCEEDED,
            ]
        );

        self::assertEquals(409, $this->client->getResponse()->getStatusCode());
    }
}
