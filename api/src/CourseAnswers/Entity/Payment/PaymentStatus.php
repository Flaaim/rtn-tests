<?php

declare(strict_types=1);

namespace App\CourseAnswers\Entity\Payment;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case SUCCEEDED = 'succeeded';
    case FAILED = 'failed';
}
