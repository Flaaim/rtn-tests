<?php

declare(strict_types=1);

namespace Tests\Functional\Testing\Attempt\Launch;

use App\Subscription\Entity\Subscription\SubscriptionId;
use App\Subscription\Test\Builder\SubscriptionBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class SubscriptionFixture extends AbstractFixture implements DependentFixtureInterface
{
    public const string ACTIVE_ID = 'c1ea3e03-f178-4276-bffb-c7a81f33e72e';
    public const string TRIAL_ID = 'feb10f77-083d-4eac-bec2-6d9631bdcffe';
    public const string EXPIRED_ID = '80ca5d34-ab20-4454-aa90-82bbb948c746';

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

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixture::class,
        ];
    }
}
