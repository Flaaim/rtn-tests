<?php

declare(strict_types=1);

namespace App\Parser\Query\Parser\HasOne;

use App\Parser\Query\Parser\ParserFetcherInterface;


final class Fetcher
{
    public function __construct(
        private readonly ParserFetcherInterface $fetcher,
    ) {}

    public function fetch(Query $query): bool
    {
        return $this->fetcher->hasOneById($query->parserId);
    }
}
