<?php

declare(strict_types=1);

namespace App\Course\Event;

final class CourseProcessed
{
    public function __construct(
        public string $courseId,
    ) {}
}
