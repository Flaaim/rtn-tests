<?php

declare(strict_types=1);

namespace App\Profile\Fixture;

use App\Auth\Fixture\UserFixture;
use App\Profile\Entity\Profile\Email;
use App\Profile\Entity\Profile\ProfileId;
use App\Profile\Entity\Profile\Role;
use App\Profile\Test\Builder\ProfileBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

/** @psalm-suppress UnusedClass */
final class ProfileFixture extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        $profile = new ProfileBuilder()
            ->withProfileId(new ProfileId(UserFixture::USER_ID))
            ->withEmail(new Email(UserFixture::USER_EMAIL))
            ->withRole(Role::admin())
            ->build();
        $manager->persist($profile);

        $trialProfile = new ProfileBuilder()
            ->withProfileId(new ProfileId(UserFixture::TRIAL_USER_ID))
            ->withEmail(new Email(UserFixture::TRIAL_USER_EMAIL))
            ->withRole(Role::user())
            ->build();
        $manager->persist($trialProfile);

        $expiredProfile = new ProfileBuilder()
            ->withProfileId(new ProfileId(UserFixture::EXPIRED_USER_ID))
            ->withEmail(new Email(UserFixture::EXPIRED_USER_EMAIL))
            ->withRole(Role::user())
            ->build();
        $manager->persist($expiredProfile);

        $manager->flush();
    }
}
