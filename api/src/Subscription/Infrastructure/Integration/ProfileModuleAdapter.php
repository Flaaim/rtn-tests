<?php

declare(strict_types=1);

namespace App\Subscription\Infrastructure\Integration;

use App\Profile\Api\ProfileApi;
use App\Subscription\Interfaces\ProfileInterface;

final readonly class ProfileModuleAdapter implements ProfileInterface
{
    public function __construct(
        private ProfileApi $profileApi,
    ) {}

    public function getProfileEmail(string $userId): ?string
    {
        return $this->profileApi->getEmail($userId);
    }
}
