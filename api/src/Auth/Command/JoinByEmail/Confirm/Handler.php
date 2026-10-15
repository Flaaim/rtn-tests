<?php

declare(strict_types=1);

namespace App\Auth\Command\JoinByEmail\Confirm;

use App\Auth\Entity\User\UserRepository;
use App\Auth\Event\GuestJoined;
use App\Infrastructure\Doctrine\Flusher;
use DateTimeImmutable;
use DomainException;
use Symfony\Component\Messenger\MessageBusInterface;

final class Handler
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly Flusher $flusher,
        private readonly MessageBusInterface $messageBus
    ) {}

    public function handle(Command $command): void
    {
        if (!$user = $this->users->findByJoinConfirmToken($command->token)) {
            throw new DomainException('Incorrect token.');
        }

        $user->confirmJoin($command->token, new DateTimeImmutable());

        $this->flusher->flush();

        if (null !== $command->guestId) {
            $this->messageBus->dispatch(
                new GuestJoined(
                    $user->getId()->getValue(),
                    $command->guestId
                )
            );
        }
    }
}
