<?php

declare(strict_types=1);

namespace App\Infrastructure\Payment;

interface PaymentGatewayInterface
{
    /**
     * @throws PaymentGatewayException
     */
    public function createPayment(
        string $actor,
        string $amount,
        string $returnUrl,
        array $metadata = [],
    ): PaymentGatewayResult;
}
