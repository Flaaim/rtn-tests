<?php

declare(strict_types=1);

namespace Tests\Functional\Testing\Attempt\Get;

use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\Functional\FixturesLoader;
use Tests\Functional\Json;
use Tests\Functional\OAuthTokenTrait;

/**
 * @internal
 * @coversNothing
 */
final class RequestActionTest extends WebTestCase
{
    use OAuthTokenTrait;
    private readonly KernelBrowser $client;
    private readonly ContainerInterface $container;
    private string $userToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();
        $this->client->disableReboot();

        $this->container = $this->client->getContainer();

        $fixturesLoader = new FixturesLoader($this->container);
        $fixturesLoader->loadFixtures([RequestFixture::class]);

        $this->userToken = $this->getAccessToken(
            $this->client,
            RequestFixture::USER_EMAIL,
            RequestFixture::USER_PASSWORD,
        );
    }

    public function testAnonymousUserReturns403(): void
    {
        $this->client->jsonRequest('GET', '/v1/testing/attempts/' . RequestFixture::ATTEMPT_ID);

        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testSuccessGuestUser(): void
    {
        $this->client->jsonRequest(
            'GET',
            '/v1/testing/attempts/' . RequestFixture::ATTEMPT_ID,
            [],
            ['HTTP_X-Guest-ID' => RequestFixture::GUEST_ID]
        );

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertArrayHasKey('id', $data);
        self::assertArrayHasKey('status', $data);
        self::assertArrayHasKey('ticketNumber', $data);
        self::assertArrayHasKey('questions', $data);
    }

    public function testSuccess(): void
    {
        $this->client->jsonRequest(
            'GET',
            '/v1/testing/attempts/' . RequestFixture::ATTEMPT_ID,
            [],
            $this->authHeaders($this->userToken)
        );

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertArrayHasKey('id', $data);
        self::assertArrayHasKey('status', $data);
        self::assertArrayHasKey('ticketNumber', $data);
        self::assertArrayHasKey('questions', $data);
    }

    public function testNotFound(): void
    {
        $this->client->jsonRequest(
            'GET',
            '/v1/testing/attempts/' . RequestFixture::ATTEMPT_ID_NOT_FOUND,
            [],
            $this->authHeaders($this->userToken)
        );

        self::assertEquals(409, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['message' => 'No attempt found.'], $data);
    }
}
