<?php

declare(strict_types=1);

namespace App\Subscription\Entity\Subscription;

enum Plan: string
{
    case BASIC = 'basic';
    case TRIAL = 'trial';

    public function isTrial(): bool
    {
        return self::TRIAL === $this;
    }
}
