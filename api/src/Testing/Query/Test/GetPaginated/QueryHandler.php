<?php

declare(strict_types=1);

namespace App\Testing\Query\Test\GetPaginated;

use App\Testing\Query\Test\TestFetcherInterface;

final class QueryHandler
{
    public function __construct(
        private readonly TestFetcherInterface $tests
    ) {}

    public function handle(Query $query): ListTestDTO
    {
        $safeLimit = max(1, $query->limit);

        $result = $this->tests->getPaginated(
            $query->page,
            $query->limit,
            $query->search
        );

        $items = array_map(
            static fn (array $row): TestDTO => TestDTO::fromArray($row),
            $result['items']
        );

        $totalCount = $result['totalCount'];

        $totalPages = $totalCount > 0 ? (int)ceil($totalCount / $safeLimit) : 0;

        return new ListTestDTO(
            items: $items,
            totalCount: $totalCount,
            totalPages: $totalPages,
        );
    }
}
