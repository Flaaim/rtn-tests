<?php

declare(strict_types=1);

namespace App\Parser\Test\Unit\Entity\Task;

use App\Parser\Entity\Task\Status;
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
        $status = new Status($name = Status::PROCESSING);

        self::assertEquals($name, $status->getValue());
    }

    public function testIncorrect(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Status('none');
    }

    public function testProcessing(): void
    {
        $status = Status::processing();
        self::assertEquals('processing', $status->getValue());
    }

    public function testEquals(): void
    {
        $status = Status::failed();

        self::assertTrue($status->isEqual($status));
        self::assertFalse($status->isEqual(Status::completed()));
    }
}
