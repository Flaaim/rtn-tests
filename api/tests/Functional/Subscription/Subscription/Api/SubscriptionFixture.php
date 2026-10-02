<?php

declare(strict_types=1);

namespace Tests\Functional\Subscription\Subscription\Api;

use App\Subscription\Entity\Subscription\Period;
use App\Subscription\Entity\Subscription\SubscriptionId;
use App\Subscription\Test\Builder\SubscriptionBuilder;
use DateTimeImmutable;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class SubscriptionFixture extends AbstractFixture implements DependentFixtureInterface
{
    public const string ACTIVE_ID = 'c1ea3e03-f178-4276-bffb-c7a81f33e72e';
    public const string TRIAL_ID = '5d8d94d2-9180-4fb0-9617-25833015b00a';
    public const string EXPIRED_ID = '34c2544f-bcae-4843-a55d-613cb608fe2a';
    public const string WAIT_NOT_READY_ID = '94bb3a5a-7df5-40cf-9d09-78b3f4e3315d';
    public const string WAIT_READY_ID = '4d8fd62f-478c-4ce4-b78e-3f264b54d4ab';

    public function load(ObjectManager $manager): void
    {
        $active = new SubscriptionBuilder()
            ->withId(new SubscriptionId(self::ACTIVE_ID))
            ->withUserId(UserFixture::USER_ID)
            ->withBasicPlan()
            ->build();
        $manager->persist($active);

        $trialUsed = new SubscriptionBuilder()
            ->withId(new SubscriptionId(self::TRIAL_ID))
            ->withUserId(UserFixture::TRIAL_USER_ID)
            ->withTrialUsed()
            ->build();
        $manager->persist($trialUsed);

        $expired = new SubscriptionBuilder()
            ->withId(new SubscriptionId(self::EXPIRED_ID))
            ->withUserId(UserFixture::EXPIRED_USER_ID)
            ->withExpiredPlan()
            ->build();
        $manager->persist($expired);

        $waitNotReady = new SubscriptionBuilder()
            ->withId(new SubscriptionId(self::WAIT_NOT_READY_ID))
            ->withUserId(UserFixture::WAIT_NOT_READY_USER_ID)
            ->withWaitPlan()
            ->build();
        $manager->persist($waitNotReady);

        $waitReady = new SubscriptionBuilder()
            ->withId(new SubscriptionId(self::WAIT_READY_ID))
            ->withUserId(UserFixture::WAIT_READY_USER_ID)
            ->withWaitPlan(Period::create(
                new DateTimeImmutable('-1 day'),
                new DateTimeImmutable('+4 days'),
            ))->build();
        $manager->persist($waitReady);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixture::class,
        ];
    }
}
