<?php

declare(strict_types=1);

namespace App\Profile\Test\Unit\Entity\Profile\Command\CreateFromSignUp;

use App\Profile\Entity\Profile\Email;
use App\Profile\Entity\Profile\Profile;
use App\Profile\Entity\Profile\ProfileId;
use App\Profile\Entity\Profile\Role;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class CreateFromSignUpTest extends TestCase
{
    public function testCreateFromSignUp(): void
    {
        $profile = Profile::createFromSignUp(
            $profileId = ProfileId::generate(),
            $email = new Email('email@test.ru'),
            $role = Role::user(),
        );

        self::assertEquals($profileId, $profile->getId());
        self::assertEquals($email, $profile->getEmail());
        self::assertEquals($role, $profile->getRole());
        self::assertNull($profile->getName());
        self::assertNull($profile->getSurname());
    }
}
