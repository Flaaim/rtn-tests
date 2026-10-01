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

    public function getById(string $id): array
    {
        $qb = $this->connection->createQueryBuilder();

        $subscription = $qb->select('s.id, s.user_id, s.status, s.plan, s.period_start, s.period_end, s.duration_days, u.email')
            ->from('subscriptions', 's')
            ->leftJoin('s', 'users', 'u', 's.user_id = u.id')
            ->where('s.id = :id')
            ->setParameter('id', $id)
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
            'duration_days' => $subscription['duration_days'],
            'email' => $subscription['email'],
        ];
    }

    public function getLatestByUserId(string $userId): array
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

    public function getPaginated(int $page = 1, int $limit = 25, ?string $search = null): array
    {
        $page = max(1, $page);
        $limit = min(max(1, $limit), 100);
        $offset = ($page - 1) * $limit;

        $qb = $this->connection->createQueryBuilder();

        $qb->from('subscriptions', 's')
            ->leftJoin('s', 'users', 'u', 's.user_id = u.id');

        $normalizedSearch = null !== $search ? trim($search) : '';

        if ('' !== $normalizedSearch) {
            $qb->andWhere(
                $qb->expr()->like('u.email', ':search')
            )->setParameter('search', '%' . $normalizedSearch . '%');
        }

        $countQb = clone $qb;
        $totalCount = (int)$countQb->select('COUNT(s.id)')
            ->executeQuery()
            ->fetchOne();

        $rows = $qb->select('s.id, s.plan, s.status, s.period_start, s.period_end, s.duration_days, u.email')
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
