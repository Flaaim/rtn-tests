<?php

declare(strict_types=1);

namespace Tests\Functional\Subscription\Subscription\GetByUser;

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
        $this->client = self::createClient();
        $this->container = $this->client->getContainer();

        $fixtureLoader = new FixturesLoader($this->container);
        $fixtureLoader->loadFixtures([RequestFixture::class]);

        $this->userToken = $this->getAccessToken(
            $this->client,
            RequestFixture::EMAIL,
            RequestFixture::PASSWORD
        );
    }

    public function testNotAuthenticatedReturn401(): void
    {
        $this->client->jsonRequest('GET', '/v1/subscriptions');

        self::assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testSuccess(): void
    {
        $this->client->jsonRequest(
            'GET',
            '/v1/subscriptions',
            [],
            $this->authHeaders($this->userToken)
        );

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertArrayHasKey('totalPages', $data);
        self::assertArrayHasKey('totalCount', $data);
        self::assertArrayHasKey('items', $data);

        self::assertCount(1, $data['items']);

        self::assertArrayHasKey('id', $data['items'][0]);
        self::assertArrayHasKey('plan', $data['items'][0]);
        self::assertArrayHasKey('durationDays', $data['items'][0]);
        self::assertArrayHasKey('periodStart', $data['items'][0]);
        self::assertArrayHasKey('periodEnd', $data['items'][0]);
    }

    public function testInvalid(): void
    {
        $query = http_build_query(['page' => -1, 'limit' => -1]);
        $this->client->jsonRequest(
            'GET',
            '/v1/subscriptions?' . $query,
            [],
            $this->authHeaders($this->userToken)
        );

        self::assertEquals(422, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'page' => 'This value should be greater than 0.',
            'limit' => 'This value should be greater than 0.',
        ]], $data);
    }
}
