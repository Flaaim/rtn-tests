<?php

declare(strict_types=1);

namespace App\Parser\Query\Parser\HasOne;

use App\Parser\Query\Parser\ParserFetcherInterface;

final class QueryHandler
{
    public function __construct(
        private readonly ParserFetcherInterface $fetcher,
    ) {}

    public function handle(Query $query): bool
    {
        return $this->fetcher->hasOneById($query->parserId);
    }
}
