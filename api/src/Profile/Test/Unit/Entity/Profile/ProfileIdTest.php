<?php

declare(strict_types=1);

namespace App\Profile\Test\Unit\Entity\Profile;

use App\Profile\Entity\Profile\ProfileId;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @internal
 * @coversNothing
 */
final class ProfileIdTest extends TestCase
{
    public function testSuccess(): void
    {
        $id = new ProfileId($value = Uuid::uuid4()->toString());

        self::assertEquals($value, $id->getValue());
    }

    public function testCase(): void
    {
        $value = Uuid::uuid4()->toString();

        $id = new ProfileId(mb_strtoupper($value));

        self::assertEquals($value, $id->getValue());
    }

    public function testGenerate(): void
    {
        $id = ProfileId::generate();

        self::assertNotEmpty($id->getValue());
    }

    public function testIncorrect(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ProfileId('12345');
    }

    public function testEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ProfileId('');
    }
}
