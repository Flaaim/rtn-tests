<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\Command\JoinByMagicLink;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Token;
use App\Auth\Entity\User\User;
use App\Auth\Test\Builder\UserBuilder;
use DateTimeImmutable;
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

        self::assertTrue($user->isActive());
    }

    public function testRequestMagicLink(): void
    {
        $user = new UserBuilder()
            ->build();

        $magicLinkToken = new Token(Uuid::uuid4()->toString(), new DateTimeImmutable('+ 1 hour'));
        $user->requestMagicLink($magicLinkToken, new DateTimeImmutable());

        self::assertEquals($magicLinkToken, $user->getMagicLinkToken());
        self::assertTrue($user->isActive());
    }
}
