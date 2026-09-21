<?php

declare(strict_types=1);

namespace Tests\Functional\Profile\GetSubscription;

use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\Functional\FixturesLoader;
use Tests\Functional\Json;
use Tests\Functional\OAuthTokenTrait;

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
        $this->client->jsonRequest('GET', '/v1/me/subscriptions');

        self::assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testSuccess(): void
    {
        $this->client->jsonRequest('GET', '/v1/me/subscriptions', [], $this->authHeaders($this->userToken));

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertArrayHasKey('hasAccess', $data);
        self::assertArrayHasKey('plan', $data);
        self::assertArrayHasKey('status', $data);
        self::assertArrayHasKey('periodStart', $data);
        self::assertArrayHasKey('periodEnd', $data);
        self::assertArrayHasKey('trialUsed', $data);
    }

}
