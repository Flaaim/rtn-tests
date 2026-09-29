<?php

declare(strict_types=1);

namespace App\Admin\Stats\Query;

use Doctrine\DBAL\Connection;

final readonly class StatsFetcher implements StatsFetcherInterface
{
    public function __construct(
        private Connection $connection
    ) {}

    public function getUsersStats(): ?array
    {
        $sql = <<<'SQL'
              SELECT
                  COUNT(id) as total_users,
                  COUNT(id) FILTER (WHERE date >= (CURRENT_DATE - INTERVAL '30 days')) as registrations_last_30_days,
                  COUNT(id) FILTER (WHERE date >= CURRENT_DATE) as registrations_today,
                  COUNT(id) FILTER (WHERE date >= (CURRENT_DATE - INTERVAL '1 week')) as registrations_this_week
              FROM users
            SQL;

        $result = $this->connection->fetchAssociative($sql);

        if (false === $result) {
            return null;
        }
        return $result;
    }

    public function getSubscriptionsStats(): ?array
    {
        $sql = <<<'SQL'
                    SELECT
                        COUNT(s.id) FILTER (WHERE s.plan = 'trial') as trial_subscriptions,
                        COUNT(s.id) FILTER (WHERE s.status = 'active') as active_subscriptions,
                        COUNT(s.id) FILTER (WHERE s.status = 'expired') as expired_subscriptions,
                        COUNT(DISTINCT s.user_id) as total_subscriptions,
                        COUNT(DISTINCT u.id) as total_users,
                    ROUND (
                        COUNT(DISTINCT s.user_id)::NUMERIC / NULLIF(COUNT(DISTINCT u.id), 0) * 100,
                        2
                    ) as conversion_rate
                FROM users u
                LEFT JOIN subscriptions s ON u.id = s.user_id
            SQL;

        $result = $this->connection->fetchAssociative($sql);
        if (false === $result) {
            return null;
        }
        return $result;
    }
}
