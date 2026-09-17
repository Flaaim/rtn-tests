<?php

declare(strict_types=1);

namespace App\Subscription\Test\Unit\Entity\Payment;

use App\Subscription\Entity\Payment\Amount;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class AmountTest extends TestCase
{
    public function testSuccess(): void
    {
        $amount = new Amount('10.00');

        self::assertEquals('10.00', $amount->getValue());
        self::assertEquals('RUB', $amount->getCurrency());
    }

    #[DataProvider('provideInvalidCases')]
    public function testInvalid($arg): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Amount must have two decimal places, e.g. 490.00');

        new Amount($arg);
    }

    public static function provideInvalidCases(): iterable
    {
        return [
            [''],
            ['1000'],
            ['10.0'],
            ['10.000'],
        ];
    }
}
