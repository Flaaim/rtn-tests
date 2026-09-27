<?php

declare(strict_types=1);

namespace App\Subscription\Query\Subscription\GetByUser;

final class ListSubscriptionDTO
{
    public function __construct(
        public array $items,
        public int $totalCount,
        public int $totalPages,
    ) {}
}
