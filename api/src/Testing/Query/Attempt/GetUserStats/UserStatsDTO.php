<?php

declare(strict_types=1);

namespace App\Testing\Query\Attempt\GetUserStats;

final class UserStatsDTO
{
    public function __construct(
        public int $completedTests,
        public int $inProgressTests,
        public float $averageScore,
        public array $popularTests,
    ) {}

    public static function fromArray(array $data): self
    {
        $popularTests = array_map(static fn (array $test) => PopularTestsDTO::fromArray($test), $data['popularTests']);

        return new self(
            completedTests: $data['completedTests'],
            inProgressTests: $data['inProgressTests'],
            averageScore: $data['averageScore'],
            popularTests: $popularTests,
        );
    }
}
