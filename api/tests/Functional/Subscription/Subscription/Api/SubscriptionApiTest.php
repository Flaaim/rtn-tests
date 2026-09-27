<?php

declare(strict_types=1);

namespace Tests\Functional\Subscription\Subscription\Api;

use App\Subscription\Api\SubscriptionApi;
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
        $fixtureLoader->loadFixtures([RequestFixture::class]);
        /** @var Connection $conn */
        $conn = $this->container->get(Connection::class);
        $this->subscriptions = new SubscriptionFetcher($conn);
    }

    public function testHasAccess(): void
    {
        /** @var SubscriptionApi $api */
        $api = $this->container->get(SubscriptionApi::class);

        self::expectNotToPerformAssertions();
        $api->ensureHasAccess(RequestFixture::USER_ID);
    }

    public function testTrialUsed(): void
    {
        /** @var SubscriptionApi $api */
        $api = $this->container->get(SubscriptionApi::class);

        self::expectException(DomainException::class);
        self::expectExceptionMessage('Ваш пробный период завершен. Для продолжения необходимо приобрести подписку.');
        $api->ensureHasAccess(RequestFixture::TRIAL_USER_ID);
    }

    public function testActivateTrial(): void
    {
        /** @var SubscriptionApi $api */
        $api = $this->container->get(SubscriptionApi::class);
        $api->ensureHasAccess(RequestFixture::NEW_USER_ID);

        self::assertTrue($this->subscriptions->isTrialUsedByUserId(RequestFixture::NEW_USER_ID));
    }
}
