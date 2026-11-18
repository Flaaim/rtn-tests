<?php

declare(strict_types=1);

namespace App\Profile\Query\GetEmail;

use App\Profile\Query\ProfileFetcherInterface;

final readonly class QueryHandler
{
    public function __construct(
        private ProfileFetcherInterface $fetcher,
    ) {}

    public function handle(Query $query): ?string
    {
        return $this->fetcher->getEmail($query->id);
    }
}
