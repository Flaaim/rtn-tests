<?php

declare(strict_types=1);

namespace App\Subscription\Test\Unit\Entity\Subscription;

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
            $durationDays = 10,
            $plan = Plan::BASIC,
            $status = Status::ACTIVE,
            $start = new DateTimeImmutable(),
            $end = new DateTimeImmutable('+ 1 day'),
        );

        self::assertEquals($id, $subscription->getId());
        self::assertEquals($durationDays, $subscription->getDurationDays());
        self::assertEquals($plan, $subscription->getPlan());
        self::assertEquals($status, $subscription->getStatus());
        self::assertEquals($start, $subscription->getPeriodStart());
        self::assertEquals($end, $subscription->getPeriodEnd());
    }

    public function testSubscriptionFailed(): void
    {
        self::expectException(DomainException::class);
        self::expectExceptionMessage('Trial Subscription Period must be exactly 1 day.');

        new Subscription(
            SubscriptionId::generate(),
            Uuid::uuid4()->toString(),
            2,
            Plan::TRIAL,
            Status::ACTIVE,
            new DateTimeImmutable(),
            new DateTimeImmutable('+ 2 day'),
        );
    }
}
