<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\CourseAnswers\Recipient\Remove;

use App\CourseAnswers\Query\Recipient\RecipientFetcher;
use App\CourseAnswers\Query\Recipient\RecipientFetcherInterface;
use Doctrine\DBAL\Connection;
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

    private readonly RecipientFetcherInterface $recipients;
    private string $adminToken;
    private string $userToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();

        $this->container = $this->client->getContainer();

        $fixturesLoader = new FixturesLoader($this->container);
        $fixturesLoader->loadFixtures([RequestFixture::class]);

        /** @var Connection $conn */
        $conn = $this->container->get(Connection::class);
        $this->recipients = new RecipientFetcher($conn);

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
        $this->client->jsonRequest('DELETE', '/v1/admin/recipients/' . RequestFixture::RECIPIENT_ID . '/remove');

        self::assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testForbiddenForRegularUser(): void
    {
        $this->client->jsonRequest(
            'DELETE',
            '/v1/admin/recipients/' . RequestFixture::RECIPIENT_ID . '/remove',
            [],
            $this->authHeaders($this->userToken)
        );

        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testSuccess(): void
    {
        $this->client->jsonRequest(
            'DELETE',
            '/v1/admin/recipients/' . RequestFixture::RECIPIENT_ID . '/remove',
            [],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(204, $this->client->getResponse()->getStatusCode());

        $result = $this->recipients->getOneById(RequestFixture::RECIPIENT_ID);
        self::assertEmpty($result);
    }

    public function testInvalid(): void
    {
        $this->client->jsonRequest(
            'DELETE',
            '/v1/admin/recipients/invalid/remove',
            [],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(422, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals([
            'errors' => ['id' => 'This is not a valid UUID.'],
        ], $data);
    }

    public function testNotFound(): void
    {
        $this->client->jsonRequest(
            'DELETE',
            '/v1/admin/recipients/' . RequestFixture::RECIPIENT_NOT_FOUND_ID . '/remove',
            [],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(409, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['message' => 'Recipient not found.'], $data);
    }
}
