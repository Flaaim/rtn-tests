<?php

declare(strict_types=1);

namespace App\Testing\Query\Test\GetAllTestIdByCourseId;

use App\Testing\Query\Test\TestFetcherInterface;

final readonly class QueryHandler
{
    public function __construct(
        private TestFetcherInterface $fetcher,
    ) {}

    /** @return TestDTO[] */
    public function handle(Query $query): array
    {
        $result = $this->fetcher->getAllTestIdByCourseId($query->courseId);

        return array_map(
            static fn (array $data): TestDTO => TestDTO::fromArray($data),
            $result
        );
    }
}
