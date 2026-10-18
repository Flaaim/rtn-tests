<?php

declare(strict_types=1);

namespace App\Testing\Query\Test\GetCompetencyMatrix;

final readonly class Query
{
    public function __construct(
        public string $actorId,
    ) {}
}
