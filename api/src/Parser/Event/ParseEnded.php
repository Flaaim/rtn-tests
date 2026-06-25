<?php

declare(strict_types=1);

namespace App\Parser\Event;

final class ParseEnded
{
    public function __construct(
        public string $taskId,
        public string $parserId,
    ) {}
}
