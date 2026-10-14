<?php

declare(strict_types=1);

namespace App\Auth\Event;

final readonly class MagicLinkRequested
{
    public function __construct(
        public string $email
    ) {}
}
