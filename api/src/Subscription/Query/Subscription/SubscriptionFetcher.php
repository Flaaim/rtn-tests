<?php

declare(strict_types=1);

namespace App\Subscription\Query\Subscription;

use Doctrine\DBAL\Connection;

/** @psalm-suppress UnusedClass */
final readonly class SubscriptionFetcher implements SubscriptionFetcherInterface
{
    public function __construct(
        private Connection $connection
    ) {}

    public function getByUserId(string $userId): array
    {
        $qb = $this->connection->createQueryBuilder();

        $subscription = $qb->select('s.id, s.user_id, s.status, s.plan, s.period_start, s.period_end')
            ->from('subscriptions', 's')
            ->where('s.user_id = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('s.period_end', 'DESC')
            ->executeQuery()
            ->fetchAssociative();

        if (false === $subscription) {
            return [];
        }
        return [
            'id' => $subscription['id'],
            'user_id' => $subscription['user_id'],
            'status' => $subscription['status'],
            'plan' => $subscription['plan'],
            'period_start' => $subscription['period_start'],
            'period_end' => $subscription['period_end'],
        ];
    }

    public function getByUserPaginated(string $userId, int $page, int $limit): array
    {
        $page = max(1, $page);
        $limit = min(max(1, $limit), 100);
        $offset = ($page - 1) * $limit;

        $qb = $this->connection->createQueryBuilder();

        $qb->from('subscriptions', 's')
            ->where('s.user_id = :userId')
            ->setParameter('userId', $userId);

        $countQb = clone $qb;
        $totalCount = (int)$countQb->select('COUNT(s.id)')
            ->executeQuery()
            ->fetchOne();

        $rows = $qb->select('s.id, s.plan, s.period_start, s.period_end, s.duration_days')
            ->orderBy('s.period_end', 'DESC')
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
