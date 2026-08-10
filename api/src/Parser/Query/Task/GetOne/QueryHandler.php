<?php

declare(strict_types=1);

namespace App\Parser\Query\Task\GetOne;

use App\Parser\Query\Task\TaskFetcher;
use DomainException;

final class QueryHandler
{
    public function __construct(
        private readonly TaskFetcher $fetcher,
    ) {}

    public function handle(Query $query): TaskDTO
    {
        $row = $this->fetcher->getOneById($query->taskId);

        if (empty($row)) {
            throw new DomainException('Task not found.');
        }

        return TaskDTO::fromArray($row);
    }
}
