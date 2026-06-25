<?php

declare(strict_types=1);

namespace App\Parser\Query\Task\Get;

use Doctrine\DBAL\Connection;

final class Fetcher
{
    public function __construct(
        private readonly Connection $connection
    ) {}

    public function fetch(Query $query): TaskDTO
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->select('t.*')
            ->from('tasks', 't')
            ->where($qb->expr()->eq('t.id', ':id'))
            ->setParameter('id', $query->taskId)
            ->executeQuery();

        $row = $qb->fetchAssociative();

        if($row === false){
            throw new \DomainException('Task not found.');
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
