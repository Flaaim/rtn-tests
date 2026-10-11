<?php

declare(strict_types=1);

namespace App\Testing\Query\Attempt\GetResult;

use DateTimeImmutable;

final class AttemptResultDTO
{
    public function __construct(
        public string $id,
        public string $status,
        public int $score,
        public int $mistakes,
        public int $ticketNumber,
        public string $startedAt,
        public TestDTO $test,
        public array $questions,
        public ?string $email = null,
        public ?string $finishedAt = null,
    ) {}

    public static function fromArray(array $data): self
    {
        $test = TestDTO::fromArray($data['test']);

        $questions = array_map(
            static fn (array $question) => QuestionDTO::fromArray($question),
            $data['questions_snapshot']
        );

        return new self(
            id: $data['id'],
            status: $data['status'],
            score: $data['score'],
            mistakes: $data['mistakes'],
            ticketNumber: $data['ticket_number'],
            startedAt: new DateTimeImmutable($data['started_at'])->format('Y-m-d H:i:s'),
            test: $test,
            questions: $questions,
            email: $data['email'],
            finishedAt: !empty($data['finished_at']) ? new DateTimeImmutable($data['finished_at'])->format('Y-m-d H:i:s') : null,
        );
    }
}
