<?php

declare(strict_types=1);

namespace App\CourseAnswers\Service;

use App\CourseAnswers\Entity\Payment\Amount;
use App\Infrastructure\Payment\PaymentGatewayResult;
use App\Infrastructure\Payment\ProductPaymentGateWayInterface;

final class ProductPaymentGateway implements ProductPaymentGateWayInterface
{
    public function createPayment(
        string $productId,
        string $email,
        Amount $amount,
        string $returnUrl,
        array $metadata = []
    ): PaymentGatewayResult {}
}
