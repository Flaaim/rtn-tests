<?php

declare(strict_types=1);

namespace App\Subscription\Event\Subscription;

use DateTimeImmutable;

final readonly class SubscriptionExpired
{
    /** @psalm-suppress PossiblyUnusedProperty */
    public string $occurredOn;

    public function __construct(
        public string $userId,
    ) {
        $this->occurredOn = new DateTimeImmutable()->format('Y-m-d');
    }
}
