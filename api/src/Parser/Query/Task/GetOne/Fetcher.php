<?php

declare(strict_types=1);

namespace App\Parser\Query\Task\GetOne;

use App\Parser\Query\Task\TaskFetcher;
use DomainException;

final class Fetcher
{
    public function __construct(
        private readonly TaskFetcher $fetcher,
    ) {}

    public function fetch(Query $query): TaskDTO
    {
        $row = $this->fetcher->getOneById($query->taskId);

        if (empty($row)) {
            throw new DomainException('Task not found.');
        }

        return new TaskDTO(
            $row['id'],
            $row['parser_id'],
            $row['status'],
            $row['branch_id'],
            $row['ticket_id']
        );
    }
}
