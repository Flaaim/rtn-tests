<?php

declare(strict_types=1);

namespace App\Parser\Query\Task\Get;

final class TaskDTO
{
    public function __construct(
        public string $taskId,
        public string $parserId,
        public string $status,
        public string $branchId,
        public string $ticketId
    ) {}
}
