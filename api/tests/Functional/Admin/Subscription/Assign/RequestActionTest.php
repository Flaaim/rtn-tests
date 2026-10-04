<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Subscription\Assign;

use App\Subscription\Query\Subscription\SubscriptionFetcher;
use App\Subscription\Query\Subscription\SubscriptionFetcherInterface;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
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
    private readonly SubscriptionFetcherInterface $subscriptions;
    private string $userToken;
    private string $adminToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();
        $this->client->disableReboot();

        $this->container = $this->client->getContainer();
        /** @var Connection $em */
        $conn = $this->container->get(Connection::class);
        $this->subscriptions = new SubscriptionFetcher($conn);

        $fixturesLoader = new FixturesLoader($this->container);
        $fixturesLoader->loadFixtures([RequestFixture::class]);

        $this->userToken = $this->getAccessToken(
            $this->client,
            RoleFixture::USER_EMAIL,
            RoleFixture::USER_PASSWORD,
        );

        $this->adminToken = $this->getAccessToken(
            $this->client,
            RoleFixture::ADMIN_EMAIL,
            RoleFixture::ADMIN_PASSWORD,
        );
    }

    public function testUnauthenticatedReturns401(): void
    {
        $this->client->jsonRequest('POST', '/v1/admin/subscriptions');

        self::assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testForbiddenForRegularUsers(): void
    {
        $this->client->jsonRequest('POST', '/v1/admin/subscriptions', [], $this->authHeaders($this->userToken));

        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAssignToNewUser(): void
    {
        $periodStart = new DateTimeImmutable('+ 1 day');
        $periodEnd = new DateTimeImmutable('+ 6 day');

        $this->client->jsonRequest(
            'POST',
            '/v1/admin/subscriptions',
            [
                'userId' => UserFixture::NEW_USER_ID,
                'plan' => 'basic',
                'periodStart' => $periodStart->format('Y-m-d'),
                'periodEnd' => $periodEnd->format('Y-m-d'),
            ],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(201, $this->client->getResponse()->getStatusCode());

        $subscriptions = $this->subscriptions->getByUserPaginated(UserFixture::NEW_USER_ID);

        self::assertCount(1, $subscriptions['items']);

        $subscription = $subscriptions['items'][0];
        self::assertEquals('basic', $subscription['plan']);

        self::assertEquals(
            $periodStart->format('Y-m-d'),
            new DateTimeImmutable($subscription['period_start'])->format('Y-m-d')
        );

        self::assertEquals(
            $periodEnd->format('Y-m-d'),
            new DateTimeImmutable($subscription['period_end'])->format('Y-m-d')
        );
    }

    public function testAssignToActive(): void
    {
        $periodStart = new DateTimeImmutable('+ 10 day');
        $periodEnd = new DateTimeImmutable('+ 15 day');

        $this->client->jsonRequest(
            'POST',
            '/v1/admin/subscriptions',
            [
                'userId' => UserFixture::USER_ID,
                'plan' => 'basic',
                'periodStart' => $periodStart->format('Y-m-d'),
                'periodEnd' => $periodEnd->format('Y-m-d'),
            ],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(201, $this->client->getResponse()->getStatusCode());

        $subscriptions = $this->subscriptions->getByUserPaginated(UserFixture::USER_ID);

        self::assertCount(2, $subscriptions['items']);
    }

    public function testAssignToExpired(): void
    {
        $periodStart = new DateTimeImmutable('+ 10 day');
        $periodEnd = new DateTimeImmutable('+ 15 day');
        $this->client->jsonRequest(
            'POST',
            '/v1/admin/subscriptions',
            [
                'userId' => UserFixture::EXPIRED_USER_ID,
                'plan' => 'basic',
                'periodStart' => $periodStart->format('Y-m-d'),
                'periodEnd' => $periodEnd->format('Y-m-d'),
            ],
            $this->authHeaders($this->adminToken)
        );
        self::assertEquals(201, $this->client->getResponse()->getStatusCode());

        $subscriptions = $this->subscriptions->getByUserPaginated(UserFixture::EXPIRED_USER_ID);

        self::assertCount(2, $subscriptions['items']);
    }

    public function testEmpty(): void
    {
        $this->client->jsonRequest(
            'POST',
            '/v1/admin/subscriptions',
            [],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(422, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'userId' => 'This value should not be blank.',
            'plan' => 'The value you selected is not a valid choice.',
            'periodStart' => 'This value should not be blank.',
            'periodEnd' => 'This value should not be blank.',
        ]], $data);
    }

    public function testInvalid(): void
    {
        $this->client->jsonRequest(
            'POST',
            '/v1/admin/subscriptions',
            [
                'userId' => 'invalid',
                'plan' => 'invalid',
                'periodStart' => 'invalid',
                'periodEnd' => 'invalid',
            ],
            $this->authHeaders($this->adminToken)
        );

        self::assertEquals(422, $this->client->getResponse()->getStatusCode());

        self::assertJson($body = $this->client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals(['errors' => [
            'userId' => 'This is not a valid UUID.',
            'plan' => 'The value you selected is not a valid choice.',
            'periodStart' => 'This value is not a valid datetime.',
            'periodEnd' => 'This value is not a valid datetime.',
        ]], $data);
    }
}
