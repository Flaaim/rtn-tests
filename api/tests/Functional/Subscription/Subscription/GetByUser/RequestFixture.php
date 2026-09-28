<?php

declare(strict_types=1);

namespace Tests\Functional\Subscription\Subscription\GetByUser;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Test\Builder\UserBuilder;
use App\Subscription\Entity\Subscription\SubscriptionId;
use App\Subscription\Test\Builder\SubscriptionBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class RequestFixture extends AbstractFixture
{
    public const string USER_ID = '39bc2486-50b5-4207-a366-323389d21507';
    public const string EMAIL = 'test@email.ru';
    public const string PASSWORD = 'password';
    public const string SUBSCRIPTION_ID = 'c1ea3e03-f178-4276-bffb-c7a81f33e72e';

    public function load(ObjectManager $manager): void
    {
        $user = new UserBuilder()
            ->withId(new Id(self::USER_ID))
            ->withEmail(new Email(self::EMAIL))
            ->withPassword(self::PASSWORD)
            ->active()
            ->build();
        $manager->persist($user);

        $activeSubscription = new SubscriptionBuilder()
            ->withId(new SubscriptionId(self::SUBSCRIPTION_ID))
            ->withUserId(self::USER_ID)
            ->withBasicPlan()
            ->build();

        $manager->persist($activeSubscription);

        $manager->flush();
    }
}
