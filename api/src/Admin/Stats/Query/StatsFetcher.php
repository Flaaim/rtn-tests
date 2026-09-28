<?php

declare(strict_types=1);

namespace App\Admin\Stats\Query;

use Doctrine\DBAL\Connection;

final readonly class StatsFetcher implements StatsFetcherInterface
{
    public function __construct(
        private Connection $connection
    ) {}

    public function getUserStats(): ?array
    {
        $sql = <<<'SQL'
              SELECT
                  COUNT(id) as total_users,
                  COUNT(id) FILTER (WHERE date >= (CURRENT_DATE - INTERVAL '30 days')) as registrations_last_30_days,
                  COUNT(id) FILTER (WHERE date >= CURRENT_DATE) as registrations_today,
                  COUNT(id) FILTER (WHERE date >= CURRENT_DATE) as registrations_this_week
              FROM users
            SQL;

        $result = $this->connection->fetchAssociative($sql);

        if(false === $result) {
            return null;
        }
        return $result;
    }
}
