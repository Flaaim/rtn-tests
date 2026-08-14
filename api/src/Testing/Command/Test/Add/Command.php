<?php

declare(strict_types=1);

namespace App\Testing\Command\Test\Add;

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
        #[Assert\Expression(
            expression: 'this.allowedMistakes <= this.numberQuestionsInTicket',
            message: 'Количество разрешенных ошибок не может превышать количество вопросов в билете.'
        )]
        public readonly int $allowedMistakes,
        #[Assert\Count(min: 1)]
        #[Assert\All(
            new Assert\Uuid()
        )]
        public readonly array $courseIds
    ) {}
}
