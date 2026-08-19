<?php

declare(strict_types=1);

namespace App\Testing\Command\Test\UpdateSettings;

use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public readonly string $id,
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
    ) {}
}
