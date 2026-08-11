<?php

declare(strict_types=1);

namespace App\Course\Query\Course\GetPaginated;

final class ListCourseDTO
{
    public function __construct(
        /** @var CourseDTO[] $items */
        public array $items,
        public int $totalCount,
        public int $totalPages,
    ) {}
}
