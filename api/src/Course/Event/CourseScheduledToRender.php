<?php

declare(strict_types=1);

namespace App\Course\Event;

final readonly class CourseScheduledToRender
{
    public function __construct(
        public string $courseId,
        public ?string $amount = null,
    ) {}
}
