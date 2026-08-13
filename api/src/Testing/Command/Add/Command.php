<?php

declare(strict_types=1);

namespace App\Testing\Command\Add;

use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $name,
        #[Assert\NotBlank]
        public readonly string $cipher,
        #[Assert\NotBlank]
        public readonly string $description,
        #[Assert\GreaterThan(0)]
        public readonly int $numberOfTickets,
        #[Assert\GreaterThan(0)]
        public readonly int $numberQuestionsInTicket,
        #[Assert\GreaterThan(0)]
        public readonly int $allowedMistakes,
        #[Assert\NotBlank]
        #[Assert\All(
            new Assert\Uuid()
        )]
        public readonly array $courseIds
    ) {}
}
