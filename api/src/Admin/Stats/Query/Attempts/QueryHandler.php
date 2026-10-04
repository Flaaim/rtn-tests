<?php

declare(strict_types=1);

namespace App\Admin\Stats\Query\Attempts;

use App\Admin\Stats\Query\StatsFetcherInterface;
use DomainException;

final readonly class QueryHandler
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private StatsFetcherInterface $fetcher,
    ) {}

    public function handle(): AttemptsStatsDTO
    {
        $result = $this->fetcher->getAttemptsStats();

        if (null === $result) {
            throw new DomainException('Can not fetch attempts statistics.');
        }

        return AttemptsStatsDTO::fromArray($result);
    }
}
