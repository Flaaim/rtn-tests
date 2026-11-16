<?php

declare(strict_types=1);

namespace App\Profile\Test\Unit\Entity\Profile;

use App\Profile\Entity\Profile\Email;
use App\Profile\Entity\Profile\Profile;
use App\Profile\Entity\Profile\ProfileId;
use App\Profile\Entity\Profile\Role;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class ProfileTest extends TestCase
{
    public function testProfile(): void
    {
        $profile = Profile::createManually(
            $id = ProfileId::generate(),
            $email = new Email('email@test.ru'),
            $role = Role::user(),
            $name = 'John',
            $surname = 'Doue'
        );
        self::assertEquals($id, $profile->getId());
        self::assertEquals($email, $profile->getEmail());
        self::assertEquals($role, $profile->getRole());
        self::assertEquals('ok', $profile->getStatus()->getValue());
        self::assertEquals($name, $profile->getName());
        self::assertEquals($surname, $profile->getSurname());
    }
}
