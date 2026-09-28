<?php

declare(strict_types=1);

namespace Tests\Functional\Payment\Confirmed;

use App\Profile\Entity\Profile\Email;
use App\Profile\Entity\Profile\ProfileId;
use App\Profile\Test\Builder\ProfileBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class ProfileFixture extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        $activeUserProfile = new ProfileBuilder()
            ->withProfileId(new ProfileId(UserFixture::USER_ID))
            ->withEmail(new Email(UserFixture::USER_EMAIL))
            ->build();
        $manager->persist($activeUserProfile);

        $newUserProfile = new ProfileBuilder()
            ->withProfileId(new ProfileId(UserFixture::NEW_USER_ID))
            ->withEmail(new Email(UserFixture::NEW_USER_EMAIL))
            ->build();
        $manager->persist($newUserProfile);

        $expiredUserProfile = new ProfileBuilder()
            ->withProfileId(new ProfileId(UserFixture::EXPIRED_USER_ID))
            ->withEmail(new Email(UserFixture::EXPIRED_USER_EMAIL))
            ->build();
        $manager->persist($expiredUserProfile);

        $staleUserProfile = new ProfileBuilder()
            ->withProfileId(new ProfileId(UserFixture::STALE_USER_ID))
            ->withEmail(new Email(UserFixture::STALE_USER_EMAIL))
            ->build();
        $manager->persist($staleUserProfile);

        $trialUserProfile = new ProfileBuilder()
            ->withProfileId(new ProfileId(UserFixture::TRIAL_USER_ID))
            ->withEmail(new Email(UserFixture::TRIAL_USER_EMAIL))
            ->build();
        $manager->persist($trialUserProfile);

        $manager->flush();
    }
}
