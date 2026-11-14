<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Event;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Role;
use App\Auth\Test\Builder\UserBuilder;
use App\Event\Entity\Event;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class RequestFixture extends AbstractFixture
{
    public const string ADMIN_EMAIL = 'admin@mail.ru';
    public const string ADMIN_PASSWORD = 'admin';

    public const string USER_EMAIL = 'user@mail.ru';
    public const string USER_PASSWORD = 'user';
    public const string AGGREGATE_ID = '2d88f00c-d81b-499a-9552-f4bc24a0abb7';
    public const string EVENT_TYPE = 'App\Auth\Event\JoinByEmailRequested';
    public const string EVENT_PAYLOAD = '';

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
            ->withEmail(new Email(self::USER_EMAIL))
            ->withPassword(self::USER_PASSWORD)
            ->active()
            ->build();
        $manager->persist($user);

        $event = new Event(
            self::AGGREGATE_ID,
            self::EVENT_TYPE,
            self::EVENT_PAYLOAD,
        );
        $manager->persist($event);
        $manager->flush();
    }
}
