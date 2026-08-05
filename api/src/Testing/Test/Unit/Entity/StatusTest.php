<?php

declare(strict_types=1);

namespace App\Testing\Test\Unit\Entity;

use App\Testing\Entity\Status;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class StatusTest extends TestCase
{
    public function testSuccess(): void
    {
        $status = new Status(Status::ACTIVE);

        self::assertEquals(Status::ACTIVE, $status->getValue());
    }

    public function testInvalid(): void
    {
        self::expectException(InvalidArgumentException::class);
        new Status('invalid');
    }

    public function testInactive(): void
    {
        $status = new Status(Status::INACTIVE);

        self::assertTrue($status->isInactive());
        self::assertFalse($status->isActive());
    }

    public function testActive(): void
    {
        $status = new Status(Status::ACTIVE);

        self::assertTrue($status->isActive());
        self::assertFalse($status->isInactive());
    }

    public function testProcessing(): void
    {
        $status = new Status(Status::PROCESSING);

        self::assertTrue($status->isProcessing());
        self::assertFalse($status->isInactive());
        self::assertFalse($status->isActive());
    }

    public function testCreated(): void
    {
        $status = new Status(Status::CREATED);
        self::assertTrue($status->isCreated());
        self::assertFalse($status->isInactive());
    }
}
