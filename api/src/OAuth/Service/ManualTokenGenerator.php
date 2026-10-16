<?php

declare(strict_types=1);

namespace App\OAuth\Service;

use App\OAuth\Entity\AccessTokenRepository;
use App\OAuth\Entity\Client;
use DateTimeImmutable;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use DomainException;
use League\OAuth2\Server\CryptKey;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class ManualTokenGenerator
{
    public function __construct(
        private AccessTokenRepository $accessToken,
        #[Autowire('%env(JWT_PRIVATE_KEY_PATH)%')]
        private string $privateKeyPath,
        #[Autowire('%env(CLIENT_SECRET)%')]
        private string $passPhrase,
        #[Autowire('%env(JWT_ENCRYPTION_KEY)%')]
        private string $encryptionKey,
    ) {}

    public function generateForUser(string $userId): array
    {
        $client = new Client($userId, 'user', '/auth');
        $accessToken = $this->accessToken->getNewToken($client, [], $userId);

        $accessToken->setIdentifier(bin2hex(random_bytes(40)));

        $accessToken->setExpiryDateTime(new DateTimeImmutable()->modify('+10 minutes'));

        $privateKey = new CryptKey($this->privateKeyPath, $this->passPhrase, false);
        $accessToken->setPrivateKey($privateKey);

        $refreshTokenId = bin2hex(random_bytes(40));
        $refreshTokenExpiry = new DateTimeImmutable()->modify('+7 days');

        $payload = [
            'client_id'        => $client->getIdentifier(),
            'refresh_token_id' => $refreshTokenId,
            'access_token_id'  => $accessToken->getIdentifier(),
            'scopes'           => [],
            'user_id'          => $userId,
            'expire_time'      => $refreshTokenExpiry->getTimestamp(),
        ];

        $key = Key::loadFromAsciiSafeString($this->encryptionKey);

        $encodedPayload = json_encode($payload);

        if (false === $encodedPayload) {
            throw new DomainException('Encoded payload is invalid.');
        }

        $refreshTokenString = Crypto::encrypt($encodedPayload, $key);

        return [
            'access_token' => $accessToken->toString(),
            'refresh_token' => $refreshTokenString,
            'expires_in' => 3600,
        ];
    }
}
