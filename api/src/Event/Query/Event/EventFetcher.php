<?php

declare(strict_types=1);

namespace App\Event\Query\Event;

use Doctrine\DBAL\Connection;

final readonly class EventFetcher implements EventFetcherInterface
{
    public function __construct(
        private Connection $connection,
    ) {}

    public function getPaginated(int $page = 1, int $limit = 15, ?string $search = null): array
    {
        $page = max(1, $page);
        $limit = min(max(1, $limit), 100);
        $offset = ($page - 1) * $limit;

        $qb = $this->connection->createQueryBuilder();

        $qb->select('e.id, e.type, e.payload, e.created_at, e.aggregate_id')
            ->from('event_store_events', 'e');

        $normalizedSearch = null !== $search ? trim($search) : '';

        if (null !== $search && '' !== trim($search)) {
            $qb->andWhere($qb->expr()->eq('e.payload', ':search'))
                ->setParameter('search', '%' . $normalizedSearch . '%');
        }

        $countQb = clone $qb;

        $totalCount = (int)$countQb->select('COUNT(e.id)')
            ->executeQuery()
            ->fetchOne();

        $rows = $qb->select('e.id, e.type, e.payload, e.created_at, e.aggregate_id')
            ->orderBy('e.created_at', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->executeQuery()
            ->fetchAllAssociative();

        return [
            'items' => $rows,
            'totalCount' => $totalCount,
        ];
    }
}
