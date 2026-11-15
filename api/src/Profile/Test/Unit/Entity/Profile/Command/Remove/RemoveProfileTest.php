<?php

declare(strict_types=1);

namespace App\Profile\Test\Unit\Entity\Profile\Command\Remove;

use App\Profile\Entity\Profile\Email;
use App\Profile\Entity\Profile\Profile;
use App\Profile\Entity\Profile\ProfileId;
use App\Profile\Entity\Profile\Role;
use App\Profile\Entity\Profile\Status;
use App\Profile\Event\ProfileRemoved;
use DomainException;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class RemoveProfileTest extends TestCase
{
    public function testRemove(): void
    {
        $profile = new Profile(
            $id = ProfileId::generate(),
            new Email('email@test.ru'),
            Role::user(),
            Status::ok(),
        );
        $profile->remove();

        $messages = $profile->releaseEvents();
        $message = end($messages);

        self::assertInstanceOf(ProfileRemoved::class, $message);
        self::assertEquals($id, $message->id);
    }

    public function testRemoveAdmin(): void
    {
        $profile = new Profile(
            ProfileId::generate(),
            new Email('email@test.ru'),
            Role::admin(),
            Status::ok(),
        );

        self::expectException(DomainException::class);
        self::expectExceptionMessage('Cannot remove admin profile.');
        $profile->remove();
    }
}
