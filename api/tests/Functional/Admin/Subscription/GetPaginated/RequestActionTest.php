<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Subscription\GetPaginated;

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
    private string $adminToken;

    protected function setUp(): void
    {
        $this->client = self::createClient();
        $this->container = $this->client->getContainer();

        $fixtureLoader = new FixturesLoader($this->container);
        $fixtureLoader->loadFixtures([RequestFixture::class]);

        $this->userToken = $this->getAccessToken(
            $this->client,
            RoleFixture::USER_EMAIL,
            RoleFixture::USER_PASSWORD
        );
        $this->adminToken = $this->getAccessToken(
            $this->client,
            RoleFixture::ADMIN_EMAIL,
            RoleFixture::ADMIN_PASSWORD
        );
    }

    public function testUnauthenticatedReturns401(): void
    {
        $this->client->jsonRequest('GET', '/v1/admin/subscriptions');

        self::assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testForbiddenForRegularUsers(): void
    {
        $this->client->jsonRequest('GET', '/v1/admin/subscriptions', [], $this->authHeaders($this->userToken));

        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testSuccess(): void
    {
        $this->client->jsonRequest(
            'GET',
            '/v1/admin/subscriptions?page=1&limit=25',
            [],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertCount(1, $data['items']);

        self::assertArrayHasKey('totalPages', $data);
        self::assertArrayHasKey('totalCount', $data);
        self::assertArrayHasKey('items', $data);

        $item = $data['items'][0];

        self::assertArrayHasKey('id', $item);
        self::assertArrayHasKey('plan', $item);
        self::assertArrayHasKey('status', $item);
        self::assertArrayHasKey('periodStart', $item);
        self::assertArrayHasKey('periodEnd', $item);
        self::assertArrayHasKey('periodEnd', $item);
        self::assertArrayHasKey('durationDays', $item);
        self::assertArrayHasKey('email', $item);
    }
}
