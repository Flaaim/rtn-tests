<?php

declare(strict_types=1);

namespace Tests\Functional\Testing\Attempt\Launch;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Test\Builder\UserBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class UserFixture extends AbstractFixture implements DependentFixtureInterface
{
    public const string USER_ID = '39bc2486-50b5-4207-a366-323389d21507';
    public const string USER_EMAIL = 'active@app.test';
    public const string USER_PASSWORD = 'user';

    public const string TRIAL_USER_ID = 'a100547d-396f-45e7-8442-fc9ad4aebdff';
    public const string TRIAL_USER_EMAIL = 'trial@app.test';

    public const string NEW_USER_ID = '15d8821d-3226-4f8d-9fb2-85c548c94119';
    public const string NEW_USER_EMAIL = 'new@app.test';

    public const string EXPIRED_USER_ID = '0281d2a7-7766-4a5d-9581-168f323fdd91';
    public const string EXPIRED_USER_EMAIL = 'expired@app.test';

    public const string WAIT_NOT_READY_USER_ID = '7074a038-b41f-42ef-9a4f-e3334ad8f4af';
    public const string WAIT_NOT_READY_USER_EMAIL = 'waitNotReady@app.test';

    public const string WAIT_READY_USER_ID = '84e41bc7-737d-433f-86bb-89c40b2d16f4';
    public const string WAIT_READY_USER_EMAIL = 'waitReady@app.test';

    public function load(ObjectManager $manager): void
    {
        $active = new UserBuilder()
            ->withId(new Id(self::USER_ID))
            ->withEmail(new Email(self::USER_EMAIL))
            ->withPassword(self::USER_PASSWORD)
            ->active()
            ->build();
        $manager->persist($active);

        $trial = new UserBuilder()
            ->withId(new Id(self::TRIAL_USER_ID))
            ->withEmail(new Email(self::TRIAL_USER_EMAIL))
            ->withPassword(self::USER_PASSWORD)
            ->active()
            ->build();
        $manager->persist($trial);

        $new = new UserBuilder()
            ->withId(new Id(self::NEW_USER_ID))
            ->withEmail(new Email(self::NEW_USER_EMAIL))
            ->withPassword(self::USER_PASSWORD)
            ->active()
            ->build();
        $manager->persist($new);

        $expired = new UserBuilder()
            ->withId(new Id(self::EXPIRED_USER_ID))
            ->withEmail(new Email(self::EXPIRED_USER_EMAIL))
            ->withPassword(self::USER_PASSWORD)
            ->active()
            ->build();
        $manager->persist($expired);

        $waitNotReady = new UserBuilder()
            ->withId(new Id(self::WAIT_NOT_READY_USER_ID))
            ->withEmail(new Email(self::WAIT_NOT_READY_USER_EMAIL))
            ->withPassword(self::USER_PASSWORD)
            ->active()
            ->build();
        $manager->persist($waitNotReady);

        $waitReady = new UserBuilder()
            ->withId(new Id(self::WAIT_READY_USER_ID))
            ->withEmail(new Email(self::WAIT_READY_USER_EMAIL))
            ->withPassword(self::USER_PASSWORD)
            ->active()
            ->build();
        $manager->persist($waitReady);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ProfileFixture::class,
        ];
    }
}
