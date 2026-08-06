<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Testing\Course\GetAll;

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
        $this->client->jsonRequest('GET', '/v1/admin/testing/courses', [], $this->authHeaders($this->adminToken));

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());
        $data = Json::decode($body);

        self::assertCount(1, $data);

        self::assertEquals([
            'courseId' => RequestFixture::COURSE_ID,
            'name' => RequestFixture::COURSE_NAME,
            'status' => 'processing',
            'createdAt' => new DateTimeImmutable()->format('Y-m-d'),
        ], $data[0]);
    }
}
