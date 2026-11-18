<?php

declare(strict_types=1);

namespace App\Course\Query\Course\GetOne;

use DateTimeImmutable;

final readonly class CourseFullDTO
{
    /** @param QuestionDTO[] $questions */
    public function __construct(
        public string $courseId,
        public string $name,
        public string $status,
        public string $createdAt,
        public string $cipher,
        public array $questions,
        public ?ProductDTO $product = null,
    ) {}

    public static function create(array $course, ?array $product): self
    {
        $questions  = array_map(
            static fn (array $question) => QuestionDTO::fromArray($question),
            $course['questions'] ?? []
        );

        return new self(
            courseId: $course['course_id'],
            name: $course['name'],
            status: $course['status'],
            createdAt: new DateTimeImmutable($course['created_at'])->format('Y-m-d'),
            cipher: $course['cipher'],
            questions: $questions,
            product: null !== $product ? ProductDTO::fromArray($product) : null,
        );
    }
}
