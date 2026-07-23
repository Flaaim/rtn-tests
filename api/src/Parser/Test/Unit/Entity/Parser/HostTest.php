<?php

declare(strict_types=1);

namespace App\Parser\Test\Unit\Entity\Parser;

use App\Parser\Entity\Parser\Host;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class HostTest extends TestCase
{
    public function testSuccess(): void
    {
        $host = new Host('https://example.com/');

        self::assertSame('https://example.com', $host->getValue());
    }

    public function testInvalid(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Неверный формат URL');
        new Host('example.com/');
    }

    public function testEmpty(): void
    {
        self::expectException(InvalidArgumentException::class);
        new Host('');
    }
}
