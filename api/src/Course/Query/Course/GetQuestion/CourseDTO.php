<?php

declare(strict_types=1);

namespace App\Course\Query\Course\GetQuestion;

final readonly class CourseDTO
{
    public function __construct(
        public string $courseId,
        public bool $isProtected,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            courseId: $data['courseId'],
            isProtected: $data['isProtected'],
        );
    }
}
