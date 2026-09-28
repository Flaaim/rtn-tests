<?php

declare(strict_types=1);

namespace App\Admin\Query;

use Doctrine\DBAL\Connection;

final readonly class AdminFetcher implements AdminFetcherInterface
{
    public function __construct(
        private Connection $connection
    ) {}

    public function getSubscriptionStats(): array
    {
        $totalUsers = (int)$this->connection->fetchOne('SELECT COUNT(id) FROM users');

        $registrationsLast30Days = (int)$this->connection->fetchOne(
            "SELECT COUNT(id) FROM users WHERE date >= (CURRENT_DATE - INTERVAL '30 days')",
        );

        return [
            'totalUsers' => $totalUsers,
            'registrationsLast30Days' => $registrationsLast30Days,
        ];
    }
}
