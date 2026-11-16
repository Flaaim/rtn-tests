<?php

declare(strict_types=1);

namespace App\Profile\Test\Unit\Entity\Profile\Command\Add;

use App\Profile\Entity\Profile\Email;
use App\Profile\Entity\Profile\Profile;
use App\Profile\Entity\Profile\ProfileId;
use App\Profile\Entity\Profile\Role;
use App\Profile\Event\ProfileAdded;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class AddProfileTest extends TestCase
{
    public function testAddProfile(): void
    {
        $profile = Profile::createManually(
            $id = ProfileId::generate(),
            $email = new Email('test@mail.ru'),
            $role = Role::user(),
        );

        $messages = $profile->releaseEvents();
        $message = end($messages);

        self::assertInstanceOf(ProfileAdded::class, $message);
        self::assertEquals($id->getValue(), $message->id);
        self::assertEquals($email->getValue(), $message->email);
        self::assertEquals($role->getName(), $message->role);
    }
}
