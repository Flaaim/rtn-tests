<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Subscription\Assign;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Test\Builder\UserBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class UserFixture extends AbstractFixture
{
    public const string USER_ID = '39bc2486-50b5-4207-a366-323389d21507';
    public const string USER_EMAIL = 'test@email.ru';

    public const string NEW_USER_ID = 'f8c3b225-d7c6-4f57-9e72-a64919de7181';
    public const string NEW_USER_EMAIL = 'new@app.test';

    public const string EXPIRED_USER_ID = '731bac7d-5df7-4799-89c8-60d54da9458b';
    public const string EXPIRED_USER_EMAIL = 'expired@app.test';
    public const string PASSWORD = 'password';

    public function load(ObjectManager $manager): void
    {
        $activeUser = new UserBuilder()
            ->withId(new Id(self::USER_ID))
            ->withEmail(new Email(self::USER_EMAIL))
            ->withPassword(self::PASSWORD)
            ->active()
            ->build();
        $manager->persist($activeUser);

        $newUser = new UserBuilder()
            ->withId(new Id(self::NEW_USER_ID))
            ->withEmail(new Email(self::NEW_USER_EMAIL))
            ->withPassword(self::PASSWORD)
            ->active()
            ->build();
        $manager->persist($newUser);

        $expiredUser = new UserBuilder()
            ->withId(new Id(self::EXPIRED_USER_ID))
            ->withEmail(new Email(self::EXPIRED_USER_EMAIL))
            ->withPassword(self::PASSWORD)
            ->active()
            ->build();
        $manager->persist($expiredUser);

        $manager->flush();
    }
}
