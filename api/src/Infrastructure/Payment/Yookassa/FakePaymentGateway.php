<?php

declare(strict_types=1);

namespace App\Infrastructure\Payment\Yookassa;

use App\Infrastructure\Payment\PaymentGatewayInterface;
use App\Infrastructure\Payment\PaymentGatewayResult;

/** @psalm-suppress UnusedClass */
final class FakePaymentGateway implements PaymentGatewayInterface
{
    public function createPayment(
        string $actor,
        string $amount,
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
