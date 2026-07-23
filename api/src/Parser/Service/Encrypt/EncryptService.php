<?php

declare(strict_types=1);

namespace App\Parser\Service\Encrypt;

use RuntimeException;

final class EncryptService implements EncryptInterface
{
    private const string METHOD = 'aes-256-ctr';

    public function __construct(
        private readonly string $secretKey
    ) {}

    public function encrypt(string $plainText): string
    {
        $ivLength = openssl_cipher_iv_length(self::METHOD);
        if (false === $ivLength) {
            throw new RuntimeException('Unable to determine IV length for cipher method.');
        }

        $iv = openssl_random_pseudo_bytes($ivLength);

        $encrypted = openssl_encrypt($plainText, self::METHOD, $this->secretKey, 0, $iv);
        if (false === $encrypted) {
            throw new RuntimeException('Encryption failed.');
        }

        return base64_encode($iv . $encrypted);
    }

    public function decrypt(string $cipherText): string
    {
        $decoded = base64_decode($cipherText, true);
        if (false === $decoded) {
            throw new RuntimeException('Invalid ciphertext encoding.');
        }

        $ivLength = openssl_cipher_iv_length(self::METHOD);
        if (false === $ivLength) {
            throw new RuntimeException('Unable to determine IV length for cipher method.');
        }

        $iv = substr($decoded, 0, $ivLength);
        $encrypted = substr($decoded, $ivLength);

        $decrypted = openssl_decrypt($encrypted, self::METHOD, $this->secretKey, 0, $iv);
        if (false === $decrypted) {
            throw new RuntimeException('Decryption failed.');
        }

        return $decrypted;
    }
}
