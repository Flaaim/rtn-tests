<?php

declare(strict_types=1);

namespace App\CourseAnswers\Query\Payment\GetResult;

final readonly class PaymentDTO
{
    public function __construct(
        public string $email,
        public string $status,
    ) {}

    public static function fromArray(array $array): self
    {
        return new self(
            email: $array['email'],
            status: $array['status'],
        );
    }
}
