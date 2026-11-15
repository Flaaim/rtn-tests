<?php

declare(strict_types=1);

namespace App\Profile\Test\Unit\Entity\Profile;

use App\Profile\Entity\Profile\Status;
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
        $status = new Status($name = Status::BANNED);

        self::assertEquals($name, $status->getValue());
    }

    public function testIncorrect(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Status('none');
    }

    public function testBanned(): void
    {
        $status = Status::banned();

        self::assertTrue($status->isBanned());
        self::assertFalse($status->isOk());
    }

    public function testOk(): void
    {
        $status = Status::ok();

        self::assertFalse($status->isBanned());
        self::assertTrue($status->isOk());
    }
}
