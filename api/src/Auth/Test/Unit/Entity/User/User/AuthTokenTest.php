<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User;

use App\Auth\Entity\User\AuthToken;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @internal
 * @coversNothing
 */
final class AuthTokenTest extends TestCase
{
    public function testSuccess(): void
    {
        $token = new AuthToken(
            $value = Uuid::uuid4()->toString(),
            $expires = new DateTimeImmutable()
        );

        self::assertEquals($value, $token->getValue());
        self::assertEquals($expires, $token->getExpiresAt());
    }

    public function testCase(): void
    {
        $value = Uuid::uuid4()->toString();

        $token = new AuthToken(mb_strtoupper($value), new DateTimeImmutable());

        self::assertEquals($value, $token->getValue());
    }

    public function testIncorrect(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new AuthToken('12345', new DateTimeImmutable());
    }

    public function testEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new AuthToken('', new DateTimeImmutable());
    }

    public function testExpired(): void
    {
        $token = new AuthToken(
            Uuid::uuid4()->toString(),
            new DateTimeImmutable()
        );

        self::assertTrue($token->isExpiredTo(new DateTimeImmutable('+ 1 day')));
        self::assertFalse($token->isExpiredTo(new DateTimeImmutable('- 1 day')));
    }
}
