<?php

declare(strict_types=1);

namespace Tests\Functional\Payment\Confirmed;

use App\Infrastructure\Doctrine\Flusher;
use App\Subscription\Entity\Subscription\Plan;
use App\Subscription\Entity\Subscription\SubscriptionRepository;
use App\Subscription\Event\Payment\PaymentConfirmed;
use App\Subscription\MessageHandler\Payment\PaymentConfirmedHandler;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Functional\FixturesLoader;

/**
 * @internal
 * @coversNothing
 */
final class HandlerTest extends KernelTestCase
{
    private ContainerInterface $container;
    private SubscriptionRepository $subscriptions;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->container = self::getContainer();

        /** @var EntityManagerInterface $em */
        $em = $this->container->get(EntityManagerInterface::class);

        $this->subscriptions = new SubscriptionRepository($em);

        $fixturesLoader = new FixturesLoader($this->container);
        $fixturesLoader->loadFixtures([RequestFixture::class]);
    }

    public function testActiveSubscription(): void
    {
        $handler = $this->container->get(PaymentConfirmedHandler::class);

        $message = new PaymentConfirmed(
            'e4225adb-3845-4b2c-b212-a39fc4614795',
            UserFixture::USER_ID,
            Plan::BASIC->value,
            5
        );
        $handler($message);

        $subscription = $this->subscriptions->findActiveByUserId(UserFixture::USER_ID);

        self::assertEquals(
            new DateTimeImmutable('- 1 day')->format('Y-m-d'),
            $subscription->getPeriodStart()->format('Y-m-d')
        );
        self::assertEquals(11, $subscription->getDurationDays());
    }

    public function testExpiredSubscription(): void
    {
        $handler = $this->container->get(PaymentConfirmedHandler::class);
        $message = new PaymentConfirmed(
            '70a44e95-d8e4-425e-ad56-9bca014c1c47',
            UserFixture::EXPIRED_USER_ID,
            Plan::BASIC->value,
            5
        );
        $handler($message);

        $subscription = $this->subscriptions->findActiveByUserId(UserFixture::EXPIRED_USER_ID);

        self::assertEquals(
            new DateTimeImmutable('now')->format('Y-m-d'),
            $subscription->getPeriodStart()->format('Y-m-d')
        );

        self::assertEquals(
            new DateTimeImmutable('+ 5 day')->format('Y-m-d'),
            $subscription->getPeriodEnd()->format('Y-m-d')
        );

        self::assertEquals(5, $subscription->getDurationDays());
    }

    public function testStaleSubscription(): void
    {
        $handler = $this->container->get(PaymentConfirmedHandler::class);
        $message = new PaymentConfirmed(
            '70a44e95-d8e4-425e-ad56-9bca014c1c47',
            UserFixture::STALE_USER_ID,
            Plan::BASIC->value,
            5
        );
        $handler($message);

        $subscription = $this->subscriptions->findActiveByUserId(UserFixture::STALE_USER_ID);

        self::assertEquals(
            new DateTimeImmutable('now')->format('Y-m-d'),
            $subscription->getPeriodStart()->format('Y-m-d')
        );

        self::assertEquals(
            new DateTimeImmutable('+ 5 day')->format('Y-m-d'),
            $subscription->getPeriodEnd()->format('Y-m-d')
        );
    }

    public function testTrialSubscription(): void
    {
        $handler = $this->container->get(PaymentConfirmedHandler::class);
        $message = new PaymentConfirmed(
            '70a44e95-d8e4-425e-ad56-9bca014c1c47',
            UserFixture::TRIAL_USER_ID,
            Plan::BASIC->value,
            5
        );
        $handler($message);

        $subscription = $this->subscriptions->findActiveByUserId(UserFixture::TRIAL_USER_ID);

        self::assertEquals(
            new DateTimeImmutable('now')->format('Y-m-d'),
            $subscription->getPeriodStart()->format('Y-m-d')
        );

        self::assertEquals(
            new DateTimeImmutable('+ 6 day')->format('Y-m-d'),
            $subscription->getPeriodEnd()->format('Y-m-d')
        );
    }

    public function testNewSubscription(): void
    {
        $handler = $this->container->get(PaymentConfirmedHandler::class);
        $message = new PaymentConfirmed(
            '70a44e95-d8e4-425e-ad56-9bca014c1c47',
            UserFixture::NEW_USER_ID,
            Plan::BASIC->value,
            5
        );
        $handler($message);

        $subscription = $this->subscriptions->findActiveByUserId(UserFixture::NEW_USER_ID);

        self::assertEquals(
            new DateTimeImmutable('now')->format('Y-m-d'),
            $subscription->getPeriodStart()->format('Y-m-d')
        );

        self::assertEquals(
            new DateTimeImmutable('+ 5 day')->format('Y-m-d'),
            $subscription->getPeriodEnd()->format('Y-m-d')
        );
    }
}
