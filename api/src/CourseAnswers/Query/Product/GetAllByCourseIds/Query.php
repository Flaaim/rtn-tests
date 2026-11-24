<?php

declare(strict_types=1);

namespace App\CourseAnswers\Query\Product\GetAllByCourseIds;

final readonly class Query
{
    public function __construct(
        public array $courseIds,
    ) {}
}
