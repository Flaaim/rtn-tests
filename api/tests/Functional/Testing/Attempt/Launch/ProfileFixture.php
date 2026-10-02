<?php

declare(strict_types=1);

namespace Tests\Functional\Testing\Attempt\Launch;

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

        $trialUserProfile = new ProfileBuilder()
            ->withProfileId(new ProfileId(UserFixture::TRIAL_USER_ID))
            ->withEmail(new Email(UserFixture::TRIAL_USER_EMAIL))
            ->build();
        $manager->persist($trialUserProfile);

        $waitNotReadyUserProfile = new ProfileBuilder()
            ->withProfileId(new ProfileId(UserFixture::WAIT_NOT_READY_USER_ID))
            ->withEmail(new Email(UserFixture::WAIT_NOT_READY_USER_EMAIL))
            ->build();
        $manager->persist($waitNotReadyUserProfile);

        $waitReadyUserProfile = new ProfileBuilder()
            ->withProfileId(new ProfileId(UserFixture::WAIT_READY_USER_ID))
            ->withEmail(new Email(UserFixture::WAIT_READY_USER_EMAIL))
            ->build();
        $manager->persist($waitReadyUserProfile);

        $manager->flush();
    }
}
