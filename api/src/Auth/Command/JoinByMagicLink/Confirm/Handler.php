<?php

declare(strict_types=1);

namespace App\Auth\Command\JoinByMagicLink\Confirm;

use App\Auth\Entity\User\UserRepository;
use App\Auth\Event\GuestJoined;
use App\Auth\Interfaces\DTO\TokenResponseDTO;
use App\Auth\Interfaces\TokenGeneratorInterface;
use App\Infrastructure\Doctrine\Flusher;
use DateTimeImmutable;
use DomainException;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class Handler
{
    public function __construct(
        private UserRepository $users,
        private Flusher $flusher,
        private MessageBusInterface $messageBus,
        private TokenGeneratorInterface $tokenGenerator,
    ) {}

    public function handle(Command $command): TokenResponseDTO
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

        return $this->tokenGenerator->generateTokenForUser($user->getId()->getValue());
    }
}
