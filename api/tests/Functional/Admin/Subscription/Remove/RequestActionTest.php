<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Subscription\Remove;

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
        $this->client->jsonRequest('DELETE', '/v1/admin/subscriptions/' . RequestFixture::SUBSCRIPTION_ID);

        self::assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testForbiddenForRegularUsers(): void
    {
        $this->client->jsonRequest(
            'DELETE',
            '/v1/admin/subscriptions/' . RequestFixture::SUBSCRIPTION_ID,
            [],
            $this->authHeaders($this->userToken)
        );

        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testSuccess(): void
    {
        $this->client->jsonRequest(
            'DELETE',
            '/v1/admin/subscriptions/' . RequestFixture::SUBSCRIPTION_ID,
            [],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(204, $this->client->getResponse()->getStatusCode());


    }

    public function testInvalid(): void
    {
        $this->client->jsonRequest(
            'DELETE',
            '/v1/admin/subscriptions/invalid',
            [],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(422, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'id' => 'This is not a valid UUID.',
        ]], $data);
    }
}
