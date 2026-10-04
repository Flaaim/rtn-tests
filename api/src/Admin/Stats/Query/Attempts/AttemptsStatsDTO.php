<?php

declare(strict_types=1);

namespace App\Admin\Stats\Query\Attempts;

final class AttemptsStatsDTO
{
    public function __construct(
        public int $totalAttempts,
        public int $attemptsToday,
        public int $attemptsThisWeek,
        public int $totalPassedAttempts,
        public float $successRate,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            totalAttempts: (int)$data['total_attempts'],
            attemptsToday: (int)$data['attempts_today'],
            attemptsThisWeek: (int)$data['attempts_this_week'],
            totalPassedAttempts: (int)$data['total_passed_attempts'],
            successRate: (float)$data['success_rate'],
        );
    }
}
