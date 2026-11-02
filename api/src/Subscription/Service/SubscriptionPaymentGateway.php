<?php

declare(strict_types=1);

namespace App\Subscription\Service;

use App\Infrastructure\Payment\PaymentGatewayInterface;
use App\Infrastructure\Payment\PaymentGatewayResult;
use App\Subscription\Entity\Payment\Amount;
use App\Subscription\Entity\Subscription\Plan;

final class SubscriptionPaymentGateway implements PaymentGatewayInterface
{

    public function createPayment(
        string $userId,
        Plan $plan,
        Amount $amount,
        int $durationDays,
        string $returnUrl,
        array $metadata = []
    ): PaymentGatewayResult
    {

    }
}
