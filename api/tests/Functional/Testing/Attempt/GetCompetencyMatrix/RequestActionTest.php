<?php

declare(strict_types=1);

namespace Tests\Functional\Testing\Attempt\GetCompetencyMatrix;

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

    public function testSuccess(): void
    {
        $this->client->catchExceptions(false);
        $this->client->jsonRequest(
            'GET',
            '/v1/testing/competency-matrix',
            [],
            $this->authHeaders($this->userToken)
        );

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertArrayHasKey('excellent', $data);
        self::assertArrayHasKey('good', $data);
        self::assertArrayHasKey('needsWork', $data);

        $excellentTest = $data['excellent'][0];
        self::assertArrayHasKey('id', $excellentTest);
        self::assertArrayHasKey('name', $excellentTest);
        self::assertArrayHasKey('cipher', $excellentTest);
        self::assertArrayHasKey('percentage', $excellentTest);
        self::assertArrayHasKey('total', $excellentTest);
    }
}
