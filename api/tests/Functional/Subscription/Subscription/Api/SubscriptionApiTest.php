<?php

declare(strict_types=1);

namespace Tests\Functional\Subscription\Subscription\Api;

use App\Subscription\Api\SubscriptionApi;
use App\Subscription\Entity\Subscription\Plan;
use App\Subscription\Entity\Subscription\Status;
use App\Subscription\Query\Subscription\SubscriptionFetcher;
use App\Subscription\Query\Subscription\SubscriptionFetcherInterface;
use Doctrine\DBAL\Connection;
use DomainException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tests\Functional\FixturesLoader;

/**
 * @internal
 * @coversNothing
 */
final class SubscriptionApiTest extends KernelTestCase
{
    private readonly ContainerInterface $container;

    private readonly SubscriptionFetcherInterface $subscriptions;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->container = self::getContainer();

        $fixtureLoader = new FixturesLoader($this->container);
        $fixtureLoader->loadFixtures([SubscriptionFixture::class]);
        /** @var Connection $conn */
        $conn = $this->container->get(Connection::class);
        $this->subscriptions = new SubscriptionFetcher($conn);
    }

    public function testHasAccess(): void
    {
        /** @var SubscriptionApi $api */
        $api = $this->container->get(SubscriptionApi::class);

        self::expectNotToPerformAssertions();
        $api->ensureHasAccess(UserFixture::USER_ID);
    }

    public function testTrialUsed(): void
    {
        /** @var SubscriptionApi $api */
        $api = $this->container->get(SubscriptionApi::class);

        self::expectException(DomainException::class);
        self::expectExceptionMessage('Ваш пробный период завершен. Для продолжения необходимо приобрести подписку.');
        $api->ensureHasAccess(UserFixture::TRIAL_USER_ID);
    }

    public function testActivateTrial(): void
    {
        /** @var SubscriptionApi $api */
        $api = $this->container->get(SubscriptionApi::class);
        $api->ensureHasAccess(UserFixture::NEW_USER_ID);

        $subscription = $this->subscriptions->getLatestByUserId(UserFixture::NEW_USER_ID);

        self::assertEquals(Plan::TRIAL->value, $subscription['plan']);
    }

    public function testExpiredPlan(): void
    {
        /** @var SubscriptionApi $api */
        $api = $this->container->get(SubscriptionApi::class);

        self::expectException(DomainException::class);
        self::expectExceptionMessage('Ваш оплаченный период завершен. Для продолжения необходимо приобрести подписку.');
        $api->ensureHasAccess(UserFixture::EXPIRED_USER_ID);
    }

    public function testWaitNotReady(): void
    {
        /** @var SubscriptionApi $api */
        $api = $this->container->get(SubscriptionApi::class);

        self::expectException(DomainException::class);
        self::expectExceptionMessage('Ваша подписка еще не началась. Доступ будет открыт в день начала оплаченного периода.');
        $api->ensureHasAccess(UserFixture::WAIT_NOT_READY_USER_ID);
    }

    public function testWaitReady(): void
    {
        /** @var SubscriptionApi $api */
        $api = $this->container->get(SubscriptionApi::class);
        $api->ensureHasAccess(UserFixture::WAIT_READY_USER_ID);

        $subscription = $this->subscriptions->getLatestByUserId(UserFixture::WAIT_READY_USER_ID);

        self::assertEquals(Status::ACTIVE->value, $subscription['status']);
    }
}
