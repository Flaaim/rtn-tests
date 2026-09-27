<?php

declare(strict_types=1);

namespace App\Subscription\Query\Subscription\Get;

use App\Subscription\Entity\Subscription\Status;

final readonly class SubscriptionDTO
{
    public function __construct(
        public bool $hasAccess,
        public string $plan,
        public string $status,
        public string $periodStart,
        public string $periodEnd
    ) {}

    public static function fromArray(array $data): self
    {
        $hasAccess = $data['status'] === Status::ACTIVE->value;

        return new self(
            hasAccess: $hasAccess,
            plan: $data['plan'],
            status: $data['status'],
            periodStart: $data['period_start'],
            periodEnd: $data['period_end']
        );
    }
}
