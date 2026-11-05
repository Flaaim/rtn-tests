<?php

declare(strict_types=1);

namespace App\Infrastructure\Payment;

enum PaymentType: string
{
    case SUBSCRIPTION = 'subscription';
    case PRODUCT = 'product';
}
