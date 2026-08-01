<?php

declare(strict_types=1);

namespace App\Parser\Query\Task\GetTasks;

use DateTimeImmutable;

final class TaskDTO
{
    public function __construct(
        public string $id,
        public string $status,
        public string $parserId,
        public string $created,
        public ?string $failedReason  = null,
    ) {}

    public static function fromArray(array $row): self
    {
        return new self(
            id: $row['task_id'],
            status: $row['status'],
            parserId: $row['parser_id'],
            created: new DateTimeImmutable($row['created_at'])->format('Y-m-d'),
            failedReason: $row['failed_reason'],
        );
    }
}
