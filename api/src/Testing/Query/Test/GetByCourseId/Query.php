<?php

declare(strict_types=1);

namespace App\Testing\Query\Test\GetByCourseId;

final readonly class Query
{
    public function __construct(
        public string $courseId
    ) {}
}
