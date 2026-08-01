<?php

declare(strict_types=1);

namespace App\Parser\Query\Task\GetTasks;

use App\Parser\Query\Task\TaskFetcherInterface;
use DomainException;

final class Fetcher
{
    public function __construct(
        private readonly TaskFetcherInterface $tasks,
    ) {}

    public function fetch(): array
    {
        $rows = $this->tasks->findAll();

        if (empty($rows)) {
            throw new DomainException('No tasks found.');
        }

        return array_map(static fn (array $row) => TaskDTO::fromArray($row), $rows);
    }
}
