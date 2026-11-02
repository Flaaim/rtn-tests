<?php

declare(strict_types=1);

namespace App\Infrastructure\Payment;

use App\Subscription\Entity\Payment\Amount;
use App\Subscription\Entity\Subscription\Plan;

interface PaymentGatewayInterface
{
    /**
     * @throws PaymentGatewayException
     */
    public function createPayment(
        string $userId,
        Plan $plan,
        Amount $amount,
        int $durationDays,
        string $returnUrl,
        array $metadata = [],
    ): PaymentGatewayResult;
}
