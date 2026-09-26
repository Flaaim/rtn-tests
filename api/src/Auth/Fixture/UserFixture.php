<?php

declare(strict_types=1);

namespace App\Auth\Fixture;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Role;
use App\Auth\Entity\User\Token;
use App\Auth\Entity\User\User;
use App\Auth\Service\PasswordHasher;
use DateTimeImmutable;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;

final class UserFixture extends AbstractFixture
{
    public const string USER_ID = 'eaa3e157-5017-4d01-84f7-3275f8e4492e';
    public const string USER_EMAIL = 'flaaim@list.ru';

    public const string TRIAL_USER_ID = 'f0809564-fdc0-4bdc-b51f-53107414b4c5';
    public const string TRIAL_USER_EMAIL = 'trial@app.test';

    public const string EXPIRED_USER_ID = '23421fa9-2cea-49f1-94fc-6498f24059ee';
    public const string EXPIRED_USER_EMAIL = 'expired@app.test';

    public const string NEW_USER_ID = '8d8dc3a3-9951-4283-b490-6d00d6b143a9';
    public const string NEW_USER_EMAIL = 'new@app.test';

    public function load(ObjectManager $manager): void
    {
        $passwordHasher = new PasswordHasher();

        $user = User::requestJoinByEmail(
            new Id(self::TRIAL_USER_ID),
            $date = new DateTimeImmutable('-30 days'),
            new Email(self::TRIAL_USER_EMAIL),
            $passwordHasher->hash('12345678'),
            new Token($value = Uuid::uuid4()->toString(), $date->modify('+1 day'))
        );

        $user->confirmJoin($value, $date);
        $manager->persist($user);

        $passwordHasher = new PasswordHasher();

        $myUser = User::requestJoinByEmail(
            new Id(self::USER_ID),
            $date = new DateTimeImmutable('-30 days'),
            new Email(self::USER_EMAIL),
            $passwordHasher->hash('12345678'),
            new Token($value = Uuid::uuid4()->toString(), $date->modify('+1 day'))
        );
        $myUser->confirmJoin($value, $date);
        $myUser->changeRole(Role::admin());
        $manager->persist($myUser);

        $expiredUser = User::requestJoinByEmail(
            new Id(self::EXPIRED_USER_ID),
            $date = new DateTimeImmutable('-30 days'),
            new Email(self::EXPIRED_USER_EMAIL),
            $passwordHasher->hash('12345678'),
            new Token($value = Uuid::uuid4()->toString(), $date->modify('+1 day'))
        );
        $expiredUser->confirmJoin($value, $date);
        $manager->persist($expiredUser);

        $newUser = User::requestJoinByEmail(
            new Id(self::NEW_USER_ID),
            $date = new DateTimeImmutable('-30 days'),
            new Email(self::NEW_USER_EMAIL),
            $passwordHasher->hash('12345678'),
            new Token($value = Uuid::uuid4()->toString(), $date->modify('+1 day'))
        );
        $newUser->confirmJoin($value, $date);
        $manager->persist($newUser);

        $manager->flush();
    }
}
