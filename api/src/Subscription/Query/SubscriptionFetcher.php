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

    public function findActiveByUserId(string $userId): array
    {
        $qb = $this->connection->createQueryBuilder();

        $subscription = $qb->select('s.id, s.user_id, s.status, s.plan, s.period_start, s.period_end, s.is_trial_used')
            ->from('subscriptions', 's')
            ->where('s.user_id = :userId')
            ->andWhere('s.status = :status')
            ->setParameter('userId', $userId)
            ->setParameter('status', Status::ACTIVE->value)
            ->orderBy('s.period_end', 'ASC')
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
            'is_trial_used' => $subscription['is_trial_used'],
        ];
    }

    public function hasActiveByUserId(string $userId): bool
    {
        $qb = $this->connection->createQueryBuilder();

        return $qb->select('COUNT(s.id)')
            ->from('subscriptions', 's')
            ->where('s.user_id = :userId')
            ->andWhere('s.plan = :plan')
            ->setParameter('userId', $userId)
            ->setParameter('plan', Plan::BASIC->value)
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
