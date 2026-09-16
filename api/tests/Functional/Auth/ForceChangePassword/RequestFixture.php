<?php

declare(strict_types=1);

namespace Tests\Functional\Auth\ForceChangePassword;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Role;
use App\Auth\Test\Builder\UserBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class RequestFixture extends AbstractFixture
{
    public const string USER_ID = '6e95c8fa-a250-4bdb-9fa3-38edf14a2a8d';
    public const string USER_EMAIL = 'email@test.ru';
    public const string USER_PASSWORD = 'password';

    public const string ADMIN_EMAIL = 'admin@mail.ru';
    public const string ADMIN_PASSWORD = 'admin';

    public function load(ObjectManager $manager): void
    {
        $admin = new UserBuilder()
            ->withEmail(new Email(self::ADMIN_EMAIL))
            ->withPassword(self::ADMIN_PASSWORD)
            ->withRole(Role::admin())
            ->active()
            ->build();
        $manager->persist($admin);

        $user = new UserBuilder()
            ->withId(new Id(self::USER_ID))
            ->withEmail(new Email(self::USER_EMAIL))
            ->withPassword(self::USER_PASSWORD)
            ->active()
            ->build();
        $manager->persist($user);

        $manager->persist($user);

        $manager->flush();
    }

    private function hash(string $password): string
    {
        return password_hash($password, PASSWORD_ARGON2I, ['memory_cost' => 16]);
    }
}
