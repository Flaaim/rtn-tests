<?php

declare(strict_types=1);

namespace App\Subscription\Fixture;

use App\Auth\Fixture\UserFixture;
use App\Subscription\Entity\Subscription\Period;
use App\Subscription\Entity\Subscription\Plan;
use App\Subscription\Entity\Subscription\Status;
use App\Subscription\Entity\Subscription\Subscription;
use App\Subscription\Entity\Subscription\SubscriptionId;
use DateTimeImmutable;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

/** @psalm-suppress UnusedClass */
final class SubscriptionFixture extends AbstractFixture
{
    public const string SUBSCRIPTION_ID = '8fda485f-1461-4848-9238-89eab7acafe1';
    public const string TRIAL_SUBSCRIPTION_ID = 'f049555e-4094-4032-b9e8-2846d600d34a';

    public const string EXPIRED_SUBSCRIPTION_ID = '3defb61f-5ac9-4ac9-baf9-b6755ccd7adb';

    public function load(ObjectManager $manager): void
    {
        $activeSubscription = Subscription::create(
            new SubscriptionId(self::SUBSCRIPTION_ID),
            UserFixture::USER_ID,
            Plan::BASIC,
            Status::ACTIVE,
            Period::create(
                new DateTimeImmutable('- 1 day'),
                new DateTimeImmutable('+ 5 days'),
            )
        );
        $manager->persist($activeSubscription);

        $trialSubscription = Subscription::create(
            new SubscriptionId(self::TRIAL_SUBSCRIPTION_ID),
            UserFixture::TRIAL_USER_ID,
            Plan::TRIAL,
            Status::ACTIVE,
            Period::create(
                new DateTimeImmutable('now '),
                new DateTimeImmutable('+ 1 day'),
            )
        );
        $manager->persist($trialSubscription);

        $expiredSubscription = Subscription::create(
            new SubscriptionId(self::EXPIRED_SUBSCRIPTION_ID),
            UserFixture::EXPIRED_USER_ID,
            Plan::BASIC,
            Status::EXPIRED,
            Period::create(
                new DateTimeImmutable('- 10 days'),
                new DateTimeImmutable('- 5 days'),
            )
        );
        $manager->persist($expiredSubscription);

        $manager->flush();
    }
}
