<?php

declare(strict_types=1);

namespace App\Auth\Command\JoinByMagicLink\Confirm;

use App\Auth\Entity\User\UserRepository;
use App\Auth\Event\GuestJoined;
use App\Infrastructure\Doctrine\Flusher;
use App\OAuth\Entity\AccessTokenRepository;
use App\OAuth\Entity\Client;
use DateTimeImmutable;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use DomainException;
use League\OAuth2\Server\CryptKey;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class Handler
{
    public function __construct(
        private UserRepository $users,
        private Flusher $flusher,
        private MessageBusInterface $messageBus,
        private AccessTokenRepository $accessToken,
        #[Autowire('%env(JWT_PRIVATE_KEY_PATH)%')]
        private string $privateKeyPath,
        #[Autowire('%env(CLIENT_SECRET)%')]
        private string $passPhrase,
        #[Autowire('%env(JWT_ENCRYPTION_KEY)%')]
        private string $encryptionKey,
    ) {}

    public function handle(Command $command): array
    {
        if (!$user = $this->users->findByMagicLinkToken($command->token)) {
            throw new DomainException('Incorrect magicLinkToken.');
        }

        $user->confirmMagicLink($command->token, new DateTimeImmutable());

        $this->flusher->flush();

        if (null !== $command->guestId) {
            $this->messageBus->dispatch(
                new GuestJoined(
                    $user->getId()->getValue(),
                    $command->guestId
                )
            );
        }

        $client = new Client($user->getId()->getValue(), 'user', '/auth');
        $accessToken = $this->accessToken->getNewToken($client, [], $user->getId()->getValue());

        $accessToken->setIdentifier(bin2hex(random_bytes(40)));

        $accessToken->setExpiryDateTime(new DateTimeImmutable()->modify('+1 hour'));

        $privateKey = new CryptKey($this->privateKeyPath, $this->passPhrase, false);
        $accessToken->setPrivateKey($privateKey);

        $refreshTokenId = bin2hex(random_bytes(40));
        $refreshTokenExpiry = new DateTimeImmutable()->modify('+30 days');

        $payload = [
            'client_id'        => $client->getIdentifier(),
            'refresh_token_id' => $refreshTokenId,
            'access_token_id'  => $accessToken->getIdentifier(),
            'scopes'           => [],
            'user_id'          => $user->getId()->getValue(),
            'expire_time'      => $refreshTokenExpiry->getTimestamp(),
        ];

        $key = Key::loadFromAsciiSafeString($this->encryptionKey);

        $encodedPayload = json_encode($payload);

        if($encodedPayload === false) {
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
