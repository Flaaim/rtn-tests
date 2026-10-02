<?php

declare(strict_types=1);

namespace App\Profile\Query\GetLookup;

use App\Profile\Query\ProfileFetcherInterface;
use DomainException;

final readonly class QueryHandler
{
    public function __construct(
        private ProfileFetcherInterface $profiles
    ) {}

    public function handle(): array
    {
        $list = $this->profiles->getLookupList();

        if (empty($list)) {
            throw new DomainException('Lookup list of profiles is empty');
        }

        return array_map(
            static fn (array $data): ProfileDTO => ProfileDTO::fromArray($data),
            $list
        );
    }
}
