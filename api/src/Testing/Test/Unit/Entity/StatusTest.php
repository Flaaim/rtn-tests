<?php

declare(strict_types=1);

namespace App\Testing\Test\Unit\Entity;

use App\Testing\Entity\Course\Status;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class StatusTest extends TestCase
{
    public function testProcessing(): void
    {
        $status = new Status(Status::PROCESSING);

        self::assertEquals(Status::PROCESSING, $status->getValue());

        self::assertTrue($status->isProcessing());
        self::assertFalse($status->isCreated());
    }

    public function testInvalid(): void
    {
        self::expectException(InvalidArgumentException::class);
        new Status('invalid');
    }

    public function testCreated(): void
    {
        $status = new Status(Status::CREATED);
        self::assertEquals(Status::CREATED, $status->getValue());

        self::assertTrue($status->isCreated());
        self::assertFalse($status->isProcessing());
    }
}
