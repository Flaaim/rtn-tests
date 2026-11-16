<?php

declare(strict_types=1);

namespace App\Profile\Test\Unit\Entity\Profile\Command\Banned;

use App\Profile\Entity\Profile\Role;
use App\Profile\Entity\Profile\Status;
use App\Profile\Test\Builder\ProfileBuilder;
use DomainException;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class BannedProfileTest extends TestCase
{
    public function testBanned(): void
    {
        $profile = new ProfileBuilder()
            ->build();

        $profile->banned();

        self::assertEquals(Status::banned(), $profile->getStatus());
    }

    public function testBannedAdmin(): void
    {
        $profile = new ProfileBuilder()
            ->withRole(Role::admin())
            ->build();

        self::expectException(DomainException::class);
        self::expectExceptionMessage('Cannot banned admin profile.');
        $profile->banned();
    }
}
