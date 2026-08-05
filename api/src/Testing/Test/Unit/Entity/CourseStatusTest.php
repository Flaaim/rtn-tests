<?php

declare(strict_types=1);

namespace App\Testing\Test\Unit\Entity;

use App\Testing\Entity\CourseStatus;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class CourseStatusTest extends TestCase
{
    public function testSuccess(): void
    {
        $status = new CourseStatus(CourseStatus::ACTIVE);

        self::assertEquals(CourseStatus::ACTIVE, $status->getValue());
    }

    public function testInvalid(): void
    {
        self::expectException(InvalidArgumentException::class);
        new CourseStatus('invalid');
    }

    public function testInactive(): void
    {
        $status = new CourseStatus(CourseStatus::INACTIVE);

        self::assertTrue($status->isInactive());
        self::assertFalse($status->isActive());
    }

    public function testActive(): void
    {
        $status = new CourseStatus(CourseStatus::ACTIVE);

        self::assertTrue($status->isActive());
        self::assertFalse($status->isInactive());
    }

    public function testProcessing(): void
    {
        $status = new CourseStatus(CourseStatus::PROCESSING);

        self::assertTrue($status->isProcessing());
        self::assertFalse($status->isInactive());
        self::assertFalse($status->isActive());
    }
}
