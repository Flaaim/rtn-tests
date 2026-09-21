<?php

declare(strict_types=1);

namespace App\Subscription\Test\Unit\Entity\Subscription;

use App\Subscription\Entity\Subscription\Period;
use App\Subscription\Entity\Subscription\Plan;
use App\Subscription\Entity\Subscription\Status;
use App\Subscription\Entity\Subscription\Subscription;
use App\Subscription\Entity\Subscription\SubscriptionId;
use DateTimeImmutable;
use DomainException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @internal
 * @coversNothing
 */
final class SubscriptionTest extends TestCase
{
    public function testSubscription(): void
    {
        $subscription = new Subscription(
            $id = SubscriptionId::generate(),
            $userId = Uuid::uuid4()->toString(),
            $plan = Plan::BASIC,
            $status = Status::ACTIVE,
            Period::create(
                $start = new DateTimeImmutable(),
                $end = new DateTimeImmutable('+ 2 days')
            )
        );

        self::assertEquals($id, $subscription->getId());
        self::assertEquals($userId, $subscription->getUserId());
        self::assertEquals(2, $subscription->getDurationDays());
        self::assertEquals($plan, $subscription->getPlan());
        self::assertEquals($status, $subscription->getStatus());
        self::assertEquals($start->format('Y-m-d'), $subscription->getPeriodStart()->format('Y-m-d'));
        self::assertEquals($end->format('Y-m-d'), $subscription->getPeriodEnd()->format('Y-m-d'));
        self::assertFalse($subscription->isTrialUsed());
    }

    public function testTrialSubscription(): void
    {
        $subscription = new Subscription(
            SubscriptionId::generate(),
            Uuid::uuid4()->toString(),
            Plan::TRIAL,
            Status::ACTIVE,
            Period::create(
                new DateTimeImmutable(),
                new DateTimeImmutable('+ 1 days')
            )
        );

        self::assertTrue($subscription->isTrialUsed());
    }

    public function testSubscriptionFailed(): void
    {
        self::expectException(DomainException::class);
        self::expectExceptionMessage('Trial Subscription Period must be exactly 1 day.');

        new Subscription(
            SubscriptionId::generate(),
            Uuid::uuid4()->toString(),
            Plan::TRIAL,
            Status::ACTIVE,
            Period::create(
                new DateTimeImmutable(),
                new DateTimeImmutable('+ 2 days')
            )
        );
    }

    public function testExtendActive(): void
    {
        $subscription = new Subscription(
            SubscriptionId::generate(),
            Uuid::uuid4()->toString(),
            Plan::BASIC,
            Status::ACTIVE,
            Period::create(
                new DateTimeImmutable(),
                new DateTimeImmutable('+ 5 days')
            )
        );

        $subscription->extend(5);

        self::assertTrue($subscription->isActive());
        self::assertEquals(10, $subscription->getDurationDays());

        self::assertEquals(new DateTimeImmutable('+ 10 days')->format('Y-m-d'), $subscription->getPeriodEnd()->format('Y-m-d'));
    }

    public function testExtendCancelled(): void
    {
        $subscription = new Subscription(
            SubscriptionId::generate(),
            Uuid::uuid4()->toString(),
            Plan::BASIC,
            Status::CANCELLED,
            Period::create(
                new DateTimeImmutable(),
                new DateTimeImmutable('+ 2 days')
            )
        );

        self::expectException(DomainException::class);
        self::expectExceptionMessage('Cancelled User Subscription cannot be extended.');
        $subscription->extend(2);
    }

    public function testExtendExpired(): void
    {
        $subscription = new Subscription(
            SubscriptionId::generate(),
            Uuid::uuid4()->toString(),
            Plan::BASIC,
            Status::EXPIRED,
            Period::create(
                $start = new DateTimeImmutable('- 10 days'),
                new DateTimeImmutable('- 5 days')
            )
        );

        $subscription->extend(5);

        $end = new DateTimeImmutable('+ 5 days');

        self::assertEquals($start->format('Y-m-d'), $subscription->getPeriodStart()->format('Y-m-d'));
        self::assertEquals($end->format('Y-m-d'), $subscription->getPeriodEnd()->format('Y-m-d'));
        self::assertEquals(5, $subscription->getDurationDays());
    }
}
