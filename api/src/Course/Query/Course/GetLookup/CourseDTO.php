<?php

declare(strict_types=1);

namespace App\Course\Query\Course\GetLookup;

final class CourseDTO
{
    public function __construct(
        public string $id,
        public string $name,
    ) {}

    public static function fromArray(array $row): self
    {
        return new self(
            id: $row['course_id'],
            name: $row['name'],
        );
    }
}
