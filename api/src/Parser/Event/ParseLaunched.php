<?php

declare(strict_types=1);

namespace App\Parser\Event;

final class ParseLaunched
{
    public function __construct(
        public string $taskId,
        public string $parserId,
        public string $branchId,
        public string $ticketId
    ) {}
}
