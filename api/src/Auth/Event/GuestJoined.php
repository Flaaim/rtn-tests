<?php

declare(strict_types=1);

namespace App\Auth\Event;

final readonly class GuestJoined
{
    public function __construct(
        public string $userId,
        public string $guestId,
    ) {}
}
