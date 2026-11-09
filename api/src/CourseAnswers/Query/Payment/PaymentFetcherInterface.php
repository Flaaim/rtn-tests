<?php

declare(strict_types=1);

namespace App\CourseAnswers\Query\Payment;

interface PaymentFetcherInterface
{
    public function getResult(string $paymentId): array;
}
