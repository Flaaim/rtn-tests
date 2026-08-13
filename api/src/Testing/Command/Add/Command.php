<?php

declare(strict_types=1);

namespace App\Testing\Command\Add;

final class Command
{
    public function __construct(
        public readonly string $name,
        public readonly string $cipher,
        public readonly string $description,
        public readonly int $numberOfTickets,
        public readonly int $numberQuestionsInTicket,
        public readonly int $allowedMistakes,
        public readonly array $courseIds
    ) {}
}
