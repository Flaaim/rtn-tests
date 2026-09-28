<?php

declare(strict_types=1);

namespace Tests\Functional\Subscription\Subscription\Api;

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
    public const string USER_EMAIL = 'test@email.ru';
    public const string TRIAL_USER_ID = '4d79d091-b22e-4639-9248-2c2d752cf67b';
    public const string TRIAL_USER_EMAIL = 'trial@app.test';

    public const string NEW_USER_ID = 'f8c3b225-d7c6-4f57-9e72-a64919de7181';
    public const string NEW_USER_EMAIL = 'new@app.test';
    public const string PASSWORD = 'password';
    public const string ACTIVE_ID = 'c1ea3e03-f178-4276-bffb-c7a81f33e72e';
    public const string TRIAL_ID = '5d8d94d2-9180-4fb0-9617-25833015b00a';

    public function load(ObjectManager $manager): void
    {
        $activeUser = new UserBuilder()
            ->withId(new Id(self::USER_ID))
            ->withEmail(new Email(self::USER_EMAIL))
            ->withPassword(self::PASSWORD)
            ->active()
            ->build();
        $manager->persist($activeUser);

        $trialUsedUser = new UserBuilder()
            ->withId(new Id(self::TRIAL_USER_ID))
            ->withEmail(new Email(self::TRIAL_USER_EMAIL))
            ->withPassword(self::PASSWORD)
            ->active()
            ->build();
        $manager->persist($trialUsedUser);

        $newUser = new UserBuilder()
            ->withId(new Id(self::NEW_USER_ID))
            ->withEmail(new Email(self::NEW_USER_EMAIL))
            ->withPassword(self::PASSWORD)
            ->active()
            ->build();
        $manager->persist($newUser);

        $active = new SubscriptionBuilder()
            ->withId(new SubscriptionId(self::ACTIVE_ID))
            ->withUserId(self::USER_ID)
            ->withBasicPlan()
            ->build();

        $manager->persist($active);

        $trialUsed = new SubscriptionBuilder()
            ->withId(new SubscriptionId(self::TRIAL_USER_ID))
            ->withUserId(self::TRIAL_USER_ID)
            ->withTrialPlan()
            ->build();

        $manager->persist($trialUsed);

        $manager->flush();
    }
}
