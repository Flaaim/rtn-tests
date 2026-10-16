<?php

declare(strict_types=1);

namespace App\Auth\Command\JoinByMagicLink\Confirm;

use App\Auth\Entity\User\UserRepository;
use App\Auth\Event\GuestJoined;
use App\Infrastructure\Doctrine\Flusher;
use App\OAuth\Service\ManualTokenGenerator;
use DateTimeImmutable;
use DomainException;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class Handler
{
    public function __construct(
        private UserRepository $users,
        private Flusher $flusher,
        private MessageBusInterface $messageBus,
        private ManualTokenGenerator $tokenGenerator,
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

        return $this->tokenGenerator->generateForUser($user->getId()->getValue());
    }
}
