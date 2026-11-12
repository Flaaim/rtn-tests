<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Course\Course\GetPaginated;

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
        $this->client->jsonRequest('GET', '/v1/admin/testing/courses', []);

        self::assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testForbiddenForRegularUser(): void
    {
        $this->client->jsonRequest('GET', '/v1/admin/testing/courses', [], $this->authHeaders($this->userToken));

        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testSuccess(): void
    {
        $this->client->jsonRequest(
            'GET',
            '/v1/admin/testing/courses?page=1&limit=15',
            [],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());
        $data = Json::decode($body);

        self::assertCount(2, $data['items']);

        self::assertArrayHasKey('totalCount', $data);
        self::assertArrayHasKey('totalPages', $data);

        $course = $data['items'][1];
        self::assertArrayHasKey('courseId', $course);
        self::assertArrayHasKey('name', $course);
        self::assertArrayHasKey('status', $course);
        self::assertArrayHasKey('createdAt', $course);
        self::assertArrayHasKey('cipher', $course);
        self::assertArrayHasKey('product', $course);

        $product = $course['product'];
        self::assertArrayHasKey('productId', $product);
        self::assertArrayHasKey('amount', $product);
        self::assertArrayHasKey('addedAt', $product);

        $courseWithOutProduct = $data['items'][0];
        self::assertNull($courseWithOutProduct['product']);
    }
}
