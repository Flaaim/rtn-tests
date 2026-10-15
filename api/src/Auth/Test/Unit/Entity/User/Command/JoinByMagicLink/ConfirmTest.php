<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\Command\JoinByMagicLink;

use App\Auth\Entity\User\Token;
use App\Auth\Event\UserJoinConfirmed;
use App\Auth\Test\Builder\UserBuilder;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @internal
 * @coversNothing
 */
final class ConfirmTest extends TestCase
{
    public function testSuccess(): void
    {
        $magicLinkToken = new Token(Uuid::uuid4()->toString(), new DateTimeImmutable('+ 1 hour'));

        $user = new UserBuilder()
            ->withMagicLinkToken($magicLinkToken)
            ->build();

        self::assertTrue($user->isWait());
        self::assertFalse($user->isActive());

        $user->confirmMagicLink($magicLinkToken->getValue(), $magicLinkToken->getExpiresAt()->modify('-1 day'));

        self::assertFalse($user->isWait());
        self::assertTrue($user->isActive());
        self::assertNull($user->getMagicLinkToken());

        self::assertNotEmpty($events = $user->releaseEvents());

        $event = end($events);

        self::assertInstanceOf(UserJoinConfirmed::class, $event);
        self::assertEquals($user->getEmail()->getValue(), $event->email);
    }
}
