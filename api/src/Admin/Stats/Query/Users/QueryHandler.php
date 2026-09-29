<?php

declare(strict_types=1);

namespace App\Admin\Stats\Query\Users;

use App\Admin\Stats\Query\StatsFetcherInterface;
use DomainException;

final readonly class QueryHandler
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private StatsFetcherInterface $fetcher,
    ) {}

    public function handle(): UsersStatsDTO
    {
        $result = $this->fetcher->getUsersStats();

        if (null === $result) {
            throw new DomainException('Can not fetch user statistics.');
        }

        return UsersStatsDTO::fromArray($result);
    }
}
