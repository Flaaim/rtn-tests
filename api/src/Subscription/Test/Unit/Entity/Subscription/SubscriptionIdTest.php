<?php

declare(strict_types=1);

namespace App\Subscription\Test\Unit\Entity\Subscription;

use App\Subscription\Entity\Subscription\SubscriptionId;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @internal
 * @coversNothing
 */
final class SubscriptionIdTest extends TestCase
{
    public function testSuccess(): void
    {
        $id = new SubscriptionId($value = Uuid::uuid4()->toString());

        self::assertEquals($value, $id->getValue());
    }

    public function testCase(): void
    {
        $value = Uuid::uuid4()->toString();

        $id = new SubscriptionId(mb_strtoupper($value));

        self::assertEquals($value, $id->getValue());
    }

    public function testGenerate(): void
    {
        $id = SubscriptionId::generate();

        self::assertNotEmpty($id->getValue());
    }

    public function testIncorrect(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new SubscriptionId('12345');
    }

    public function testEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new SubscriptionId('');
    }
}
