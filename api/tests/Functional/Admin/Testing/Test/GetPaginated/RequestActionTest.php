<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Testing\Test\GetPaginated;

use DateTimeImmutable;
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
    private string $adminToken;
    private string $userToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();
        $this->container = $this->client->getContainer();

        $fixturesLoader = new FixturesLoader($this->container);
        $fixturesLoader->loadFixtures([RequestFixture::class]);

        $this->adminToken = $this->getAccessToken(
            $this->client,
            RequestFixture::ADMIN_EMAIL,
            RequestFixture::ADMIN_PASSWORD,
        );

        $this->userToken = $this->getAccessToken(
            $this->client,
            RequestFixture::USER_EMAIL,
            RequestFixture::USER_PASSWORD,
        );
    }

    public function testUnauthenticatedReturns401(): void
    {
        $this->client->jsonRequest('GET', '/v1/admin/testing/tests');

        self::assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testForbiddenForRegularUser(): void
    {
        $this->client->jsonRequest('GET', '/v1/admin/testing/tests', [], $this->authHeaders($this->userToken));

        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testSuccess(): void
    {
        $this->client->jsonRequest(
            'GET',
            '/v1/admin/testing/tests?page=1&limit=15',
            [],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());
        $data = Json::decode($body);

        self::assertCount(1, $data['items']);

        self::assertEquals([
            'items' => [
                [
                    'testId' => RequestFixture::TEST_ID,
                    'name' => RequestFixture::TEST_NAME,
                    'cipher' => RequestFixture::TEST_CIPHER,
                    'status' => 'inactive',
                    'createdAt' => new DateTimeImmutable()->format('Y-m-d'),
                ],
            ],
            'totalCount' => 1,
            'totalPages' => 1,
        ], $data);
    }
}
