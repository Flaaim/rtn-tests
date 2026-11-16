<?php

declare(strict_types=1);

namespace Tests\Functional\Admin\Profile\GetFull;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Role as UserRole;
use App\Auth\Test\Builder\UserBuilder;
use App\Profile\Entity\Profile\Email as ProfileEmail;
use App\Profile\Entity\Profile\ProfileId;
use App\Profile\Entity\Profile\Role;
use App\Profile\Test\Builder\ProfileBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class RequestFixture extends AbstractFixture
{
    public const string USER_ID = '2a974384-bcf1-4c48-ab4b-59b241420436';
    public const string ADMIN_ID = '238d5e02-0a7b-4cbb-8aea-e8873458d287';

    public const string USER_EMAIL = 'user@mail.ru';
    public const string USER_PASSWORD = 'user';

    public const string ADMIN_EMAIL = 'admin@mail.ru';
    public const string ADMIN_PASSWORD = 'admin';

    public function load(ObjectManager $manager): void
    {
        $user = new UserBuilder()
            ->withId(new Id(self::USER_ID))
            ->withEmail(new Email(self::USER_EMAIL))
            ->withPassword(self::USER_PASSWORD)
            ->active()
            ->build();
        $manager->persist($user);

        $userProfile = new ProfileBuilder()
            ->withProfileId(new ProfileId(self::USER_ID))
            ->withEmail(new ProfileEmail(self::USER_EMAIL))
            ->withRole(Role::user())
            ->build();

        $manager->persist($userProfile);

        $admin = new UserBuilder()
            ->withEmail(new Email(self::ADMIN_EMAIL))
            ->withPassword(self::ADMIN_PASSWORD)
            ->withRole(UserRole::admin())
            ->active()
            ->build();
        $manager->persist($admin);

        $adminProfile = new ProfileBuilder()
            ->withProfileId(new ProfileId(self::ADMIN_ID))
            ->withEmail(new ProfileEmail(self::ADMIN_EMAIL))
            ->withRole(Role::admin())
            ->build();

        $manager->persist($adminProfile);

        $manager->flush();
    }
}
