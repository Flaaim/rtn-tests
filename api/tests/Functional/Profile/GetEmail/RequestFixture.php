<?php

declare(strict_types=1);

namespace Tests\Functional\Profile\GetEmail;

use App\Auth\Entity\User\Email as UserEmail;
use App\Auth\Entity\User\Id;
use App\Auth\Test\Builder\UserBuilder;
use App\Profile\Entity\Profile\Email as ProfileEmail;
use App\Profile\Entity\Profile\ProfileId;
use App\Profile\Test\Builder\ProfileBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class RequestFixture extends AbstractFixture
{
    public const string ID = '00000000-0000-0000-0000-000000000002';
    public const string NOT_FOUND_ID = '00000000-0000-0000-0000-000000000003';
    public const string EMAIL = 'test@email.ru';
    public const string PASSWORD = 'password';

    public function load(ObjectManager $manager): void
    {
        $user = new UserBuilder()
            ->withId(new Id(self::ID))
            ->withEmail(new UserEmail(self::EMAIL))
            ->withPassword(self::PASSWORD)
            ->active()
            ->build();
        $manager->persist($user);

        $profile = new ProfileBuilder()
            ->withProfileId(new ProfileId(self::ID))
            ->withEmail(new ProfileEmail(self::EMAIL))
            ->build();
        $manager->persist($profile);

        $manager->flush();
    }
}
