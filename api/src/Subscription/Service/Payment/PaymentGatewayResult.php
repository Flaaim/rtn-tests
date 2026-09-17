<?php

declare(strict_types=1);

namespace App\Subscription\Service\Payment;

final readonly class PaymentGatewayResult
{
    public function __construct(
        public string $externalId,
        public string $confirmationUrl,
    ) {}
}
