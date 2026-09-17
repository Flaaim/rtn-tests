<?php

declare(strict_types=1);

namespace App\Subscription\Service\Payment;

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
    ): PaymentGatewayResult;
}
