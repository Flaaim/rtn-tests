<?php

declare(strict_types=1);

namespace App\Testing\Interfaces;

interface SubscriptionInterface
{
    public function ensureHasAccess(string $userId): void;
}
