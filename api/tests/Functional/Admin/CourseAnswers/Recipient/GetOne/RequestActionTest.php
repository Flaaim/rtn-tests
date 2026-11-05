<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\CourseAnswers\Recipient\GetOne;

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
            RoleFixture::ADMIN_EMAIL,
            RoleFixture::ADMIN_PASSWORD,
        );

        $this->userToken = $this->getAccessToken(
            $this->client,
            RoleFixture::USER_EMAIL,
            RoleFixture::USER_PASSWORD,
        );
    }

    public function testUnauthenticatedReturns401(): void
    {
        $this->client->jsonRequest('GET', '/v1/admin/recipients/' . RequestFixture::RECIPIENT_ID);

        self::assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testForbiddenForRegularUser(): void
    {
        $this->client->jsonRequest(
            'GET',
            '/v1/admin/recipients/' . RequestFixture::RECIPIENT_ID,
            [],
            $this->authHeaders($this->userToken)
        );

        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testInvalid(): void
    {
        $this->client->jsonRequest(
            'GET',
            '/v1/admin/recipients/invalid',
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

    public function testNotFound(): void
    {
        $this->client->jsonRequest(
            'GET',
            '/v1/admin/recipients/' . RequestFixture::RECIPIENT_NOT_FOUND_ID,
            [],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(409, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['message' => 'Recipient not found.'], $data);
    }

    public function testSuccess(): void
    {
        $this->client->jsonRequest(
            'GET',
            '/v1/admin/recipients/' . RequestFixture::RECIPIENT_ID,
            [],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertArrayHasKey('recipientId', $data);
        self::assertArrayHasKey('email', $data);
        self::assertArrayHasKey('status', $data);
        self::assertArrayHasKey('product', $data);
        self::assertArrayHasKey('createdAt', $data);
        self::assertArrayHasKey('failedReason', $data);
        self::assertArrayHasKey('sendAt', $data);

        $product = $data['product'];
        self::assertArrayHasKey('productId', $product);
        self::assertArrayHasKey('cipher', $product);
    }
}
