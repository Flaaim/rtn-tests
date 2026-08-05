<?php

declare(strict_types=1);

namespace App\Testing\Event;

final class CourseCreated
{
    public function __construct(
        public string $id,
    ) {}
}
