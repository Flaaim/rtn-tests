<?php

declare(strict_types=1);

namespace App\Parser\Test\Unit\Entity\Parser;

use App\Parser\Entity\Parser\Credentials;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class CredentialsTest extends TestCase
{
    public function testCreate(): void
    {
        $credentials = new Credentials(
            $login = 'test',
            $password = 'password',
        );

        self::assertEquals($login, $credentials->getLogin());
        self::assertEquals($password, $credentials->getPassword());
    }
}
