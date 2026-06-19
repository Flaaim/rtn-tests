<?php

declare(strict_types=1);

namespace App\Parser\Test\Unit\Service;

use App\Parser\Service\GlueCookie;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class GlueCookieTest extends TestCase
{
    public function testSuccess(): void
    {
        $cookie = new GlueCookie();
        $result = $cookie->glue([
            'zero',
            'one',
            'two',
            'three',
        ]);
        self::assertEquals('two three zero', $result);
    }

    public function testEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $cookie = new GlueCookie();
        $cookie->glue([]);
    }
}
