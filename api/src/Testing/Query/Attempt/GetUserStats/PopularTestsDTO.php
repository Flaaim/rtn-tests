<?php

declare(strict_types=1);

namespace App\Testing\Query\Attempt\GetUserStats;

final readonly class PopularTestsDTO
{
    public function __construct(
        public string $name,
        public string $cipher,
        public int $testsAttempts,
        public float $averageScore,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            cipher: $data['cipher'],
            testsAttempts: $data['testsAttempts'],
            averageScore: $data['averageScore'],
        );
    }
}
