<?php

declare(strict_types=1);

namespace App\Subscription\Query\Subscription\GetPaginated;

final readonly class ListSubscriptionDTO
{
    public function __construct(
        /** @var SubscriptionDTO[] $items */
        public array $items,
        public int $totalCount,
        public int $totalPages,
    ) {}
}
