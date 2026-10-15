<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\Command\JoinByMagicLink;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Token;
use App\Auth\Entity\User\User;
use App\Auth\Test\Builder\UserBuilder;
use DateTimeImmutable;
use DomainException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @internal
 * @coversNothing
 */
final class RequestTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = User::requestJoinByMagicLink(
            $id = Id::generate(),
            $date = new DateTimeImmutable(),
            $email = new Email('mail@example.com'),
        );

        self::assertEquals($id, $user->getId());
        self::assertEquals($date, $user->getDate());
        self::assertEquals($email, $user->getEmail());

        self::assertTrue($user->isWait());
    }

    public function testRequestMagicLink(): void
    {
        $user = new UserBuilder()
            ->build();

        $magicLinkToken = new Token(Uuid::uuid4()->toString(), new DateTimeImmutable('+ 1 hour'));
        $user->requestMagicLink($magicLinkToken, new DateTimeImmutable());

        self::assertEquals($magicLinkToken, $user->getMagicLinkToken());
    }

    public function testRequestMagicLinkAlready(): void
    {
        $magicLinkToken = new Token(Uuid::uuid4()->toString(), new DateTimeImmutable('+ 1 hour'));
        $user = new UserBuilder()
            ->withMagicLinkToken($magicLinkToken)
            ->build();

        self::expectException(DomainException::class);
        self::expectExceptionMessage('Ссылка уже отправлена. Пожалуйста, проверьте почту.');

        $user->requestMagicLink($magicLinkToken, new DateTimeImmutable());
    }
}
