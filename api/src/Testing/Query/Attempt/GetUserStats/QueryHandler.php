<?php

declare(strict_types=1);

namespace App\Testing\Query\Attempt\GetUserStats;

use App\Testing\Query\Attempt\AttemptFetcherInterface;

final class QueryHandler
{
    public function __construct(
        private readonly AttemptFetcherInterface $fetcher,
    ) {}

    public function handle(Query $query): UserStatsDTO
    {
        $result = $this->fetcher->getUserStats($query->userId);

        return new UserStatsDTO(
            completedTests: $result['completedTests'],
            inProgressTests: $result['inProgressTests'],
            averageScore: $result['averageScore']
        );
    }
}
