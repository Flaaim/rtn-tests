<?php

declare(strict_types=1);

namespace App\Testing\Query\Test\GetCompetencyMatrix;

final readonly class MatrixDTO
{
    public function __construct(
        public array $excellent,
        public array $good,
        public array $needsWork,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            excellent: $data['excellent'],
            good: $data['good'],
            needsWork: $data['needsWork'],
        );
    }
}
