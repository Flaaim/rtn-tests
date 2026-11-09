<?php

declare(strict_types=1);

namespace App\CourseAnswers\Query\Payment\GetResult;

use DateTimeImmutable;

final readonly class PaymentDTO
{
    public function __construct(
        public string $paymentId,
        public string $amount,
        public string $currency,
        public string $email,
        public string $status,
        public string $createdAt,
    ) {}

    public static function fromArray(array $array): self
    {
        return new self(
            paymentId: $array['payment_id'],
            amount: $array['amount'],
            currency: $array['currency'],
            email: $array['email'],
            status: $array['status'],
            createdAt: new DateTimeImmutable($array['created_at'])->format('Y-m-d'),
        );
    }
}
