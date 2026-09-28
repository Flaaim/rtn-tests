<?php

declare(strict_types=1);

namespace App\Admin\Query\GetSubscriptionStats;

final class SubscriptionStatsDTO
{
    public function __construct(
        public int $totalUsers,
        public int $registrationsLast30Days,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            totalUsers: $data['totalUsers'],
            registrationsLast30Days: $data['registrationsLast30Days'],
        );
    }
}
