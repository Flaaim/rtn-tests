<?php

declare(strict_types=1);

namespace Tests\Functional\Payment\Confirmed;

use App\Subscription\Entity\Subscription\Period;
use App\Subscription\Entity\Subscription\SubscriptionId;
use App\Subscription\Test\Builder\SubscriptionBuilder;
use DateTimeImmutable;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class RequestFixture extends AbstractFixture implements DependentFixtureInterface
{
    public const string ACTIVE_SUBSCRIPTION_ID = '6fe314c2-0e32-4531-a06a-940a29e90313';
    public const string EXPIRED_SUBSCRIPTION_ID = '5e5e30dc-ef88-4331-97cb-822da54260bf';
    public const string STALE_SUBSCRIPTION_ID = '49dcc914-11eb-42fd-8f14-4d84a074bba2';
    public const string TRIAL_SUBSCRIPTION_ID = '159e20bf-d587-4271-a6f9-809727d163df';
    public const string NEW_SUBSCRIPTION_ID = '83b01cc7-944a-494d-b36b-09994918fa16';

    public function load(ObjectManager $manager): void
    {
        $activeSubscription = new SubscriptionBuilder()
            ->withId(new SubscriptionId(self::ACTIVE_SUBSCRIPTION_ID))
            ->withUserId(UserFixture::USER_ID)
            ->withBasicPlan(Period::create(
                new DateTimeImmutable('- 1 day'),
                new DateTimeImmutable('+ 5 days'),
            ))
            ->build();
        $manager->persist($activeSubscription);

        $expiredSubscription = new SubscriptionBuilder()
            ->withId(new SubscriptionId(self::EXPIRED_SUBSCRIPTION_ID))
            ->withUserId(UserFixture::EXPIRED_USER_ID)
            ->withExpiredPlan()
            ->build();
        $manager->persist($expiredSubscription);

        $staleSubscription = new SubscriptionBuilder()
            ->withId(new SubscriptionId(self::STALE_SUBSCRIPTION_ID))
            ->withUserId(UserFixture::STALE_USER_ID)
            ->withStaleActivePlan()
            ->build();
        $manager->persist($staleSubscription);

        $trialSubscription = new SubscriptionBuilder()
            ->withId(new SubscriptionId(self::TRIAL_SUBSCRIPTION_ID))
            ->withUserId(UserFixture::TRIAL_USER_ID)
            ->withTrialPlan()
            ->build();
        $manager->persist($trialSubscription);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixture::class,
        ];
    }
}
