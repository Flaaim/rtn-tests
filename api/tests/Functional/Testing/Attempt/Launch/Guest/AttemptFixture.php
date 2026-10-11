<?php

declare(strict_types=1);

namespace Tests\Functional\Testing\Attempt\Launch\Guest;

use App\Testing\Entity\Attempt\Attempt;
use App\Testing\Entity\Attempt\AttemptId;
use App\Testing\Entity\Attempt\Status;
use DateTimeImmutable;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class AttemptFixture extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        $attempt = new Attempt(
            AttemptId::generate(),
            RequestFixture::TEST_ID,
            RequestFixture::GUEST_ID_ATTEMPT_EXCEEDED,
            Status::passed(),
            new DateTimeImmutable(),
            RequestFixture::TICKET_NUMBER,
            [],
            8,
            2
        );
        $manager->persist($attempt);

        $attempt = new Attempt(
            AttemptId::generate(),
            RequestFixture::TEST_ID,
            RequestFixture::GUEST_ID_ATTEMPT_EXCEEDED,
            Status::passed(),
            new DateTimeImmutable(),
            RequestFixture::TICKET_NUMBER,
            [],
            8,
            2
        );
        $manager->persist($attempt);

        $manager->flush();
    }
}
