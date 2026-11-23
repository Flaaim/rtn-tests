<?php

declare(strict_types=1);

namespace App\Testing\Query\Test\GetAllWithCourseId;

final readonly class TestDTO
{
    public function __construct(
        public string $testId,
        public array $courseIds,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            testId: $data['id'],
            courseIds: $data['course_ids'],
        );
    }
}
