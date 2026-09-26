<?php

declare(strict_types=1);

namespace App\Subscription\Fixture;

use App\Auth\Fixture\UserFixture;
use App\Subscription\Entity\Subscription\Period;
use App\Subscription\Entity\Subscription\Plan;
use App\Subscription\Entity\Subscription\Status;
use App\Subscription\Entity\Subscription\Subscription;
use App\Subscription\Entity\Subscription\SubscriptionId;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

/** @psalm-suppress UnusedClass */
final class SubscriptionFixture extends AbstractFixture
{
    public const string SUBSCRIPTION_ID = '8fda485f-1461-4848-9238-89eab7acafe1';
    public const string TRIAL_SUBSCRIPTION_ID = 'f049555e-4094-4032-b9e8-2846d600d34a';
    public function load(ObjectManager $manager): void
    {
        $activeSubscription = new Subscription(
            new SubscriptionId(self::SUBSCRIPTION_ID),
            UserFixture::USER_ID,
            Plan::BASIC,
            Status::ACTIVE,
            Period::create(
                new \DateTimeImmutable('- 1 day'),
                new \DateTimeImmutable('+ 5 days'),
            )
        );
        $manager->persist($activeSubscription);

        $trialSubscription = new Subscription(
            new SubscriptionId(self::TRIAL_SUBSCRIPTION_ID),
            UserFixture::TRIAL_USER_ID,
            Plan::TRIAL,
            Status::ACTIVE,
            Period::create(
                new \DateTimeImmutable('now '),
                new \DateTimeImmutable('+ 1 day'),
            )
        );
        $manager->persist($trialSubscription);

        $manager->flush();
    }
}
