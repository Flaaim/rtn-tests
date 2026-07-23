<?php

declare(strict_types=1);

namespace App\Parser\Service\Encrypt;

interface EncryptInterface
{
    public function encrypt(string $plainText): string;

    public function decrypt(string $cipherText): string;
}
