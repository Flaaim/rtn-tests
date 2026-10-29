<?php

declare(strict_types=1);

namespace App\Course\Command\Course\RenderToFile;

final readonly class Command
{
    public function __construct(
        public string $courseId,
    ) {}
}
