<?php

declare(strict_types=1);

namespace App\Parser\Query\Task;

use Doctrine\DBAL\Connection;

final class TaskFetcher implements TaskFetcherInterface
{
    public function __construct(
        private readonly Connection $connection
    ) {}

    public function getOneById(string $id): array
    {
        $qb = $this->connection->createQueryBuilder();
        $result = $qb->select('t.*')
            ->from('tasks', 't')
            ->where($qb->expr()->eq('t.id', ':id'))
            ->setParameter('id', $id)
            ->executeQuery();

        $result = $result->fetchAssociative();

        if (false === $result) {
            return [];
        }
        return $result;
    }

    public function findAll(): array
    {
        $qb = $this->connection->createQueryBuilder();

        $result = $qb->select('t.task_id, t.status, t.parser_id, t.created_at, t.failed_reason')
            ->from('tasks', 't')
            ->executeQuery();

        return $result->fetchAllAssociative();
    }
}
