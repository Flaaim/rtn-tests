<?php

declare(strict_types=1);

namespace App\Admin\Stats\Query\Users;

final readonly class UsersStatsDTO
{
    public function __construct(
        public int $totalUsers,
        public int $registrationsToday,
        public int $registrationsThisWeek,
        public int $registrationsLast30Days,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            totalUsers: (int)$data['total_users'],
            registrationsToday: (int)$data['registrations_today'],
            registrationsThisWeek: (int)$data['registrations_this_week'],
            registrationsLast30Days: (int)$data['registrations_last_30_days'],
        );
    }
}
