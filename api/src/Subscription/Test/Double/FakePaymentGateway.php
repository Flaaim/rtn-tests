<?php

declare(strict_types=1);

namespace App\Subscription\Test\Double;

use App\Subscription\Entity\Payment\Amount;
use App\Subscription\Entity\Subscription\Plan;
use App\Subscription\Service\Payment\PaymentGatewayInterface;
use App\Subscription\Service\Payment\PaymentGatewayResult;

/** @psalm-suppress UnusedClass */
final class FakePaymentGateway implements PaymentGatewayInterface
{
    public function createPayment(
        string $userId,
        Plan $plan,
        Amount $amount,
        int $durationDays,
        string $returnUrl
    ): PaymentGatewayResult {
        $separator = str_contains($returnUrl, '?') ? '&' : '?';

        return new PaymentGatewayResult(
            externalId: 'test-yookassa-payment-' . uniqid('', true),
            confirmationUrl: $returnUrl . $separator . 'gateway=fake',
        );
    }
}
