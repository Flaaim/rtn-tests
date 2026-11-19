<?php

declare(strict_types=1);

namespace App\Testing\Query\Test\GetByCourseId;

use App\Testing\Query\Test\TestFetcherInterface;

final readonly class QueryHandler
{
    public function __construct(
        private TestFetcherInterface $fetcher
    ) {}

    public function handle(Query $query): ?TestDTO
    {
        $test = $this->fetcher->getOneByCourseId($query->courseId);

        if (null === $test) {
            return null;
        }

        return TestDTO::fromArray($test);
    }
}
