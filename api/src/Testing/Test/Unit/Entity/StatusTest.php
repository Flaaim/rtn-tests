<?php

declare(strict_types=1);

namespace App\Testing\Test\Unit\Entity;

use App\Testing\Entity\Test\Status;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class StatusTest extends TestCase
{
    public function testActive(): void
    {
        $status = new Status(Status::ACTIVE);

        self::assertEquals(Status::ACTIVE, $status->getValue());

        self::assertTrue($status->isActive());
        self::assertFalse($status->isInactive());
    }

    public function testInvalid(): void
    {
        self::expectException(InvalidArgumentException::class);
        new Status('invalid');
    }

    public function testInactive(): void
    {
        $status = new Status(Status::INACTIVE);
        self::assertEquals(Status::INACTIVE, $status->getValue());

        self::assertTrue($status->isInactive());
        self::assertFalse($status->isActive());
    }
}
