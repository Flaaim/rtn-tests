<?php

declare(strict_types=1);

namespace App\Parser\Test\Unit\Service;

use App\Parser\Service\EncryptService;
use PHPUnit\Framework\TestCase;

final class EncryptServiceTest extends TestCase
{
    public function testEncrypt(): void
    {
        $encrypt = new EncryptService('secret');

        $encryptString = $encrypt->encrypt('password');

        self::assertEquals('password', $encrypt->decrypt($encryptString));

    }
}
