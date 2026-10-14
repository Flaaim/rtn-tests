<?php

declare(strict_types=1);

namespace Tests\Functional\Testing\Attempt\GetResult;

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
        $this->client->jsonRequest('GET', '/v1/testing/attempts/' . RequestFixture::ATTEMPT_ID . '/result');

        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testSuccess(): void
    {
        $this->client->jsonRequest(
            'GET',
            '/v1/testing/attempts/' . RequestFixture::ATTEMPT_ID . '/result',
            [],
            $this->authHeaders($this->userToken)
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
        self::assertArrayHasKey('questions', $data);
        self::assertArrayHasKey('email', $data);

        self::assertArrayHasKey('test', $data);
        self::assertArrayHasKey('id', $data['test']);
        self::assertArrayHasKey('name', $data['test']);
        self::assertArrayHasKey('cipher', $data['test']);
        self::assertArrayHasKey('allowedMistakes', $data['test']);
        self::assertArrayHasKey('slug', $data['test']);

        self::assertArrayHasKey('category', $data);
        self::assertArrayHasKey('name', $data['category']);
        self::assertArrayHasKey('slug', $data['category']);
        self::assertArrayHasKey('parent', $data['category']);
    }

    public function testNotFound(): void
    {
        $this->client->jsonRequest(
            'GET',
            '/v1/testing/attempts/' . RequestFixture::ATTEMPT_ID_NOT_FOUND . '/result',
            [],
            $this->authHeaders($this->userToken)
        );

        self::assertEquals(409, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals([
            'message' => 'Attempt result not found.',
        ], $data);
    }
}
