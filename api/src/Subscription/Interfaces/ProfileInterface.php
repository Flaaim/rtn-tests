<?php

declare(strict_types=1);

namespace App\Subscription\Interfaces;

interface ProfileInterface
{
    public function getProfileEmail(string $userId): ?string;
}
