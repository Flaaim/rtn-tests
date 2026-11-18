<?php

declare(strict_types=1);

namespace App\Testing\Query\Test\GetByCourseId;

use App\Testing\Query\Test\TestFetcherInterface;
use DomainException;

final readonly class QueryHandler
{
    public function __construct(
        private TestFetcherInterface $fetcher
    ) {}

    public function handle(Query $query): ?TestDTO
    {
        $test = $this->fetcher->getOneByCourseId($query->courseId);

        if (null === $test) {
            throw new DomainException('Test not found.');
        }

        return TestDTO::fromArray($test);
    }
}
