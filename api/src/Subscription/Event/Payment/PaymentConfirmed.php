<?php

declare(strict_types=1);

namespace App\Subscription\Event\Payment;

use DateTimeImmutable;

final readonly class PaymentConfirmed
{
    /** @psalm-suppress PossiblyUnusedProperty */
    public string $occurredOn;

    public function __construct(
        public string $paymentId,
        public string $userId,
        public string $plan,
        public int $durationDays,
    ) {
        $this->occurredOn = new DateTimeImmutable()->format('Y-m-d');
    }
}
