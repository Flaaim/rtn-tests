<?php

declare(strict_types=1);

namespace App\CourseAnswers\Query\Product\GetByCourseId;

final readonly class Query
{
    public function __construct(
        public string $courseId,
    ) {}
}
