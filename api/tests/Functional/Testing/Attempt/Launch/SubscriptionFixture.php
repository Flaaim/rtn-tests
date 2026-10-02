<?php

declare(strict_types=1);

namespace Tests\Functional\Testing\Attempt\Launch;

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
    public const string TRIAL_ID = 'feb10f77-083d-4eac-bec2-6d9631bdcffe';
    public const string EXPIRED_ID = '80ca5d34-ab20-4454-aa90-82bbb948c746';
    public const string WAIT_NOT_READY_ID = '31976b34-1055-47b8-94ee-cc41146d02d2';
    public const string WAIT_READY_ID = 'a6a170e2-b61a-40c5-bfee-babc24591b19';

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
                new DateTimeImmutable('+4 day')
            ))
            ->build();
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
