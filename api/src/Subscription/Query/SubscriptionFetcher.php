<?php

declare(strict_types=1);

namespace App\Subscription\Query;

use App\Subscription\Entity\Subscription\Plan;
use App\Subscription\Entity\Subscription\Status;
use Doctrine\DBAL\Connection;

/** @psalm-suppress UnusedClass */
final readonly class SubscriptionFetcher implements SubscriptionFetcherInterface
{
    public function __construct(
        private Connection $connection
    ) {}

    public function findByUserId(string $userId): array
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

    public function hasActiveByUserId(string $userId): bool
    {
        $qb = $this->connection->createQueryBuilder();

        return $qb->select('COUNT(s.id)')
            ->from('subscriptions', 's')
            ->where('s.user_id = :userId')
            ->andWhere('s.plan = :plan')
            ->andWhere('s.status = :status')
            ->setParameter('userId', $userId)
            ->setParameter('plan', Plan::BASIC->value)
            ->setParameter('status', Status::ACTIVE->value)
            ->executeQuery()
            ->fetchOne();
    }

    public function hasTrialByUserId(string $userId): bool
    {
        $qb = $this->connection->createQueryBuilder();

        return $qb->select('COUNT(s.id)')
            ->from('subscriptions', 's')
            ->where('s.user_id = :userId')
            ->andWhere('s.plan = :plan')
            ->setParameter('userId', $userId)
            ->setParameter('plan', Plan::TRIAL->value)
            ->executeQuery()
            ->fetchOne();
    }
}
