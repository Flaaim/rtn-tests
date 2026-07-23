<?php

declare(strict_types=1);

namespace App\Parser\Service\Encrypt;

final class EncryptService implements EncryptInterface
{
    private const string METHOD = 'aes-256-ctr';

    public function __construct(
        private readonly string $secretKey
    ) {}

    public function encrypt(string $plainText): string
    {
        $ivLength = openssl_cipher_iv_length(self::METHOD);
        $iv = openssl_random_pseudo_bytes($ivLength);

        $encrypted = openssl_encrypt($plainText, self::METHOD, $this->secretKey, 0, $iv);

        return base64_encode($iv . $encrypted);
    }

    public function decrypt(string $cipherText): string
    {
        $decoded = base64_decode($cipherText, true);
        $ivLength = openssl_cipher_iv_length(self::METHOD);

        $iv = substr($decoded, 0, $ivLength);
        $encrypted = substr($decoded, $ivLength);

        return openssl_decrypt($encrypted, self::METHOD, $this->secretKey, 0, $iv);
    }
}
