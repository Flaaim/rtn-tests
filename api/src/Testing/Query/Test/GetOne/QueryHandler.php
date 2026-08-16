<?php

declare(strict_types=1);

namespace App\Testing\Query\Test\GetOne;

use App\Testing\Query\Test\TestFetcherInterface;
use DomainException;

final class QueryHandler
{
    public function __construct(
        private readonly TestFetcherInterface $tests
    ) {}

    public function handle(Query $query): TestFullDTO
    {
        $row = $this->tests->getOneById($query->id);

        if (empty($row)) {
            throw new DomainException('Test not found.');
        }

        return TestFullDTO::fromArray($row);
    }
}
