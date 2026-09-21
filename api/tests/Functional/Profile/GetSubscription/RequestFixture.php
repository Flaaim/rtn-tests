<?php

declare(strict_types=1);

namespace Tests\Functional\Profile\GetSubscription;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Test\Builder\UserBuilder;
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

        $activeSubscription = new Subscription(
            new SubscriptionId(self::SUBSCRIPTION_ID),
            self::USER_ID,
            Plan::BASIC,
            Status::ACTIVE,
            Period::create(
                new DateTimeImmutable('- 1 day'),
                new DateTimeImmutable('+ 5 days'),
            )
        );
        $manager->persist($activeSubscription);

        $manager->flush();
    }
}
