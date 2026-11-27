<?php

declare(strict_types=1);

namespace App\CourseAnswers\Command\Recipient\Remove;

use App\CourseAnswers\Entity\Recipient\RecipientId;
use App\CourseAnswers\Entity\Recipient\RecipientRepository;
use App\Infrastructure\Doctrine\Flusher;

final readonly class Handler
{
    public function __construct(
        private RecipientRepository $recipients,
        private Flusher $flusher
    ) {}

    public function handle(Command $command): void
    {
        $recipient = $this->recipients->get(new RecipientId($command->id));

        $recipient->remove();

        $this->recipients->remove($recipient);

        $this->flusher->flush();
    }
}
