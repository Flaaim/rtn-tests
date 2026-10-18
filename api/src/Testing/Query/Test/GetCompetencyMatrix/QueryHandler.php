<?php

declare(strict_types=1);

namespace App\Testing\Query\Test\GetCompetencyMatrix;

use App\Testing\Query\Test\TestFetcherInterface;

final readonly class QueryHandler
{
    public function __construct(
        private TestFetcherInterface $fetcher,
    ) {}

    public function handle(Query $query): MatrixDTO
    {
        $result = $this->fetcher->getCompetencyMatrix($query->actorId);

        return MatrixDTO::fromArray($result);
    }
}
