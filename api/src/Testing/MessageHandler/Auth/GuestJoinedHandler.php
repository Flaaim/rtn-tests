<?php

declare(strict_types=1);

namespace App\Testing\MessageHandler\Auth;

use App\Auth\Event\GuestJoined;
use App\Infrastructure\Doctrine\Flusher;
use App\Testing\Entity\Attempt\Attempt;
use App\Testing\Entity\Attempt\AttemptRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/** @psalm-suppress UnusedClass */
#[AsMessageHandler]
final readonly class GuestJoinedHandler
{
    public function __construct(
        private AttemptRepository $attempts,
        private Flusher $flusher,
    ) {}

    public function __invoke(GuestJoined $event): void
    {
        $guestId = $event->guestId;
        $userId = $event->userId;

        $guestAttempts = $this->attempts->findByUserId($guestId);

        foreach ($guestAttempts as $attempt) {
            /** @var Attempt $attempt */
            $attempt->assignToUser($userId);
        }
        $this->flusher->flush();
    }
}
