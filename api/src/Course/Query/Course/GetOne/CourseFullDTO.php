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

    public static function fromArray(array $data): self
    {
        $questions  = array_map(
            static fn (array $question) => QuestionDTO::fromArray($question),
            $data['questions'] ?? []
        );

        $product = null;

        if (isset($data['product'])) {
            $product = ProductDTO::fromArray($data['product']);
        }

        return new self(
            courseId: $data['course_id'],
            name: $data['name'],
            status: $data['status'],
            createdAt: new DateTimeImmutable($data['created_at'])->format('Y-m-d'),
            cipher: $data['cipher'],
            questions: $questions,
            product: $product,
        );
    }
}
