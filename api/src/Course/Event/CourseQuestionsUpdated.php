<?php

declare(strict_types=1);

namespace App\Course\Event;

final readonly class CourseQuestionsUpdated
{
    public function __construct(
        public string $courseId
    ) {}
}
