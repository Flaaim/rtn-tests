<?php

declare(strict_types=1);

namespace App\Course\Event;

final readonly class CourseRendered
{
    public function __construct(
        public string $cipher,
        public string $filePath,
        public ?string $amount = null
    ) {}
}
