<?php

declare(strict_types=1);

namespace App\Testing\Query\Course\GetOne;

use DateTimeImmutable;

final class CourseFullDTO
{
    /** @param QuestionDTO[] $questions */
    public function __construct(
        public readonly string $courseId,
        public readonly string $name,
        public readonly string $status,
        public readonly string $createdAt,
        public readonly string $cipher,
        public readonly array $questions,
    ) {}

    public static function fromArray(array $data): self
    {
        $questions  = array_map(
            static fn (array $question) => QuestionDTO::fromArray($question),
            $data['questions'] ?? []
        );

        return new self(
            courseId: $data['course_id'],
            name: $data['name'],
            status: $data['status'],
            createdAt: new DateTimeImmutable($data['created_at'])->format('Y-m-d'),
            cipher: $data['cipher'],
            questions: $questions,
        );
    }
}
