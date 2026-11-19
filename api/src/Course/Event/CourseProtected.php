<?php

declare(strict_types=1);

namespace App\Course\Event;

final readonly class CourseProtected
{
    public function __construct(
        public string $courseId,
        public string $cipher,
    ) {}
}
