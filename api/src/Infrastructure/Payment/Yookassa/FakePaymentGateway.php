<?php

declare(strict_types=1);

namespace App\Infrastructure\Payment\Yookassa;

use App\Infrastructure\Payment\PaymentGatewayInterface;
use App\Infrastructure\Payment\PaymentGatewayResult;
use App\Subscription\Entity\Payment\Amount;
use App\Subscription\Entity\Subscription\Plan;

/** @psalm-suppress UnusedClass */
final class FakePaymentGateway implements PaymentGatewayInterface
{
    public function createPayment(
        string $userId,
        Plan $plan,
        Amount $amount,
        int $durationDays,
        string $returnUrl,
        array $metadata = []
    ): PaymentGatewayResult {
        $separator = str_contains($returnUrl, '?') ? '&' : '?';

        return new PaymentGatewayResult(
            externalId: 'test-yookassa-payment-' . uniqid('', true),
            confirmationUrl: $returnUrl . $separator . 'gateway=fake',
        );
    }
}
