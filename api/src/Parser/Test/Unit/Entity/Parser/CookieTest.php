<?php

declare(strict_types=1);

namespace App\Parser\Test\Unit\Entity\Parser;

use App\Parser\Entity\Parser\Cookie;
use PHPUnit\Framework\TestCase;

final class CookieTest extends TestCase
{
    public function testSuccess(): void
    {
        $value = 'WorkplaceToken=2e33e862-ae48-41df-b235-e46a31b9d1f0; path=/; expires=Sat, 17 Nov 2525 04:55:04 GMT, .OLIMPAUTH=; path=/Admin; expires=Mon, 11 Oct 1999 17:00:00 GMT, .OLIMPAUTH=VDRPimD1Ga4eF1fg9A4hvE6H/dqcbhMxlzPPKIIDK1GK2dn3chNltN6k+wTlCEnOJmRq67DAi6yUQIxV6rHLo5HP/1Jcanntl3B5rp1goeK0r5RZb3OYByRYzN3YWHZP; path=/Admin, .OLIMPROLES=; path=/Admin; expires=Mon, 11 Oct 1999 17:00:00 GMT';

        $cookie = new Cookie($value);
        self::assertEquals($value, $cookie->getValue());
    }

    public function testEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Cookie('');
    }
}
