<?php

declare(strict_types=1);

namespace App\Course\Event;

final class CourseUpdated
{
    public function __construct(
        public string $id,
    ) {}
}
