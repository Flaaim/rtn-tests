<?php

declare(strict_types=1);

namespace App\Parser\Query\Task\GetOne;

use DateTimeImmutable;

final class TaskDTO
{
    public function __construct(
        public string $taskId,
        public string $status,
        public string $created,
        public ?string $draft = null,
        public ?string $failed_reason = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            taskId: $data['task_id'],
            status: $data['status'],
            created: new DateTimeImmutable($data['created_at'])->format('Y-m-d'),
            draft: $data['draft'],
            failed_reason: $data['failed_reason'],
        );
    }
}
