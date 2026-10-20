<?php

declare(strict_types=1);

namespace Tests\Functional\Testing\Analytics\GetPopular;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
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

    public function testUnauthenticatedReturns401(): void
    {
        $this->client->jsonRequest('GET', '/v1/testing/analytics/popular');

        self::assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testSuccess(): void
    {
        $this->client->catchExceptions(false);
        $this->client->jsonRequest('GET', '/v1/testing/analytics/popular', [], $this->authHeaders($this->userToken));

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertCount(1, $data);
        self::assertArrayHasKey('id', $data[0]);
        self::assertArrayHasKey('name', $data[0]);
        self::assertArrayHasKey('cipher', $data[0]);
        self::assertArrayHasKey('testsAttempts', $data[0]);
        self::assertArrayHasKey('averageScore', $data[0]);
    }
}
