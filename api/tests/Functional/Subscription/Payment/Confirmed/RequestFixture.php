<?php

declare(strict_types=1);

namespace Tests\Functional\Subscription\Payment\Confirmed;

use App\Subscription\Entity\Subscription\Period;
use App\Subscription\Entity\Subscription\Plan;
use App\Subscription\Entity\Subscription\Status;
use App\Subscription\Entity\Subscription\Subscription;
use App\Subscription\Entity\Subscription\SubscriptionId;
use DateTimeImmutable;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class RequestFixture extends AbstractFixture
{
    public const string ACTIVE_USER_ID = '684c670e-01d3-458e-b781-610572c3ca79';
    public const string EXPIRED_USER_ID = '1beae977-73bf-43cd-a2bc-ec06457ba06a';
    public const string ACTIVE_SUBSCRIPTION_ID = '6fe314c2-0e32-4531-a06a-940a29e90313';
    public const string EXPIRED_SUBSCRIPTION_ID = '5e5e30dc-ef88-4331-97cb-822da54260bf';

    public function load(ObjectManager $manager): void
    {
        $activeSubscription = new Subscription(
            new SubscriptionId(self::ACTIVE_SUBSCRIPTION_ID),
            self::ACTIVE_USER_ID,
            Plan::BASIC,
            Status::ACTIVE,
            Period::create(
                new DateTimeImmutable('- 1 day'),
                new DateTimeImmutable('+ 5 days'),
            )
        );
        $manager->persist($activeSubscription);

        $expiredSubscription = new Subscription(
            new SubscriptionId(self::EXPIRED_SUBSCRIPTION_ID),
            self::EXPIRED_USER_ID,
            Plan::BASIC,
            Status::EXPIRED,
            Period::create(
                new DateTimeImmutable('- 10 day'),
                new DateTimeImmutable('- 5 days'),
            )
        );
        $manager->persist($expiredSubscription);

        $manager->flush();
    }
}
