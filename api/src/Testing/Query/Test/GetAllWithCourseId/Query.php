<?php

declare(strict_types=1);

namespace App\Testing\Query\Test\GetAllWithCourseId;

final readonly class Query
{
    public function __construct(
        public string $courseId,
    ) {}
}
