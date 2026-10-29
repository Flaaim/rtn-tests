<?php

declare(strict_types=1);

namespace App\CourseAnswers\Event\Payment;

final class PaymentConfirmed
{
    public function __construct(
        public string $email,
        public string $courseId,
    ) {}
}
