<?php

declare(strict_types=1);

namespace App\Subscription\Entity\Subscription;

enum Status: string
{
    case ACTIVE = 'active';
    case EXPIRED = 'expired';
    case CANCELLED = 'cancelled';
}
