<?php

declare(strict_types=1);

namespace App\Testing\Query\Attempt\GetPopularTests;

use App\Testing\Query\Attempt\AttemptFetcherInterface;
use DomainException;

final readonly class QueryHandler
{
    public function __construct(
        private AttemptFetcherInterface $fetcher,
    ) {}

    public function handle(Query $query): array
    {
        $rows = $this->fetcher->getPopularTests($query->userId);
        if (empty($rows)) {
            throw new DomainException('No found popular tests.');
        }

        return array_map(static fn (array $data): PopularTestsDTO => PopularTestsDTO::fromArray($data), $rows);
    }
}
