<?php

declare(strict_types=1);

namespace App\Subscription\Test\Unit\Entity\Payment;

use App\Subscription\Entity\Payment\PaymentId;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @internal
 * @coversNothing
 */
final class PaymentIdTest extends TestCase
{
    public function testSuccess(): void
    {
        $id = new PaymentId($value = Uuid::uuid4()->toString());

        self::assertEquals($value, $id->getValue());
    }

    public function testCase(): void
    {
        $value = Uuid::uuid4()->toString();

        $id = new PaymentId(mb_strtoupper($value));

        self::assertEquals($value, $id->getValue());
    }

    public function testGenerate(): void
    {
        $id = PaymentId::generate();

        self::assertNotEmpty($id->getValue());
    }

    public function testIncorrect(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new PaymentId('12345');
    }

    public function testEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new PaymentId('');
    }
}
