<?php

declare(strict_types=1);

namespace App\Testing\Query\Test\GetOne;

final class TicketDTO
{
    private function __construct(
        public int $number,
        public array $questions
    ) {}

    public static function fromArray(array $data): self
    {
        $questions = array_map(
            static fn (array $questionData) => QuestionDTO::fromArray($questionData),
            $data['questions'] ?? []
        );

        return new self(
            number: $data['number'],
            questions: $questions
        );
    }
}
