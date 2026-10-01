<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Subscription\Get;

use App\Auth\Entity\User\Id;
use App\Auth\Test\Builder\UserBuilder;
use App\Subscription\Entity\Subscription\SubscriptionId;
use App\Subscription\Test\Builder\SubscriptionBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class RequestFixture extends AbstractFixture implements DependentFixtureInterface
{
    public const string SUBSCRIPTION_ID = 'c1ea3e03-f178-4276-bffb-c7a81f33e72e';
    public const string USER_ID = '98c7d4cf-bc29-4fd6-baa7-09cd4161bdcf';

    public function load(ObjectManager $manager): void
    {
        $user = new UserBuilder()
            ->withId(new Id(self::USER_ID))
            ->build();
        $manager->persist($user);

        $activeSubscription = new SubscriptionBuilder()
            ->withId(new SubscriptionId(self::SUBSCRIPTION_ID))
            ->withUserId($user->getId()->getValue())
            ->withBasicPlan()
            ->build();
        $manager->persist($activeSubscription);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            RoleFixture::class,
        ];
    }
}
