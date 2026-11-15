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

        if ('' !== $normalizedSearch) {
            $isUuid = preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $normalizedSearch);
            $isNumeric = ctype_digit($normalizedSearch);

            if ($isUuid || $isNumeric) {
                $qb->andWhere(
                    $qb->expr()->or(
                        'e.aggregate_id = :exact_search',
                        'e.payload ILIKE :like_search'
                    )
                )->setParameter('exact_search', $normalizedSearch);
            } else {
                $qb->andWhere('e.payload ILIKE :like_search');
            }
            $qb->setParameter('like_search', '%' . $normalizedSearch . '%');
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
