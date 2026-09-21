<?php

declare(strict_types=1);

namespace App\Subscription\Query\GetSubscription;

final readonly class Query
{
    public function __construct(
        public string $userId,
    ) {}
}
