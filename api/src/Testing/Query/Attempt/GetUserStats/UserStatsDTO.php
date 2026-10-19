<?php

declare(strict_types=1);

namespace App\Testing\Query\Attempt\GetUserStats;

final class UserStatsDTO
{
    public function __construct(
        public int $completedTests,
        public int $inProgressTests,
        public float $averageScore,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            completedTests: $data['completedTests'],
            inProgressTests: $data['inProgressTests'],
            averageScore: $data['averageScore']
        );
    }
}
