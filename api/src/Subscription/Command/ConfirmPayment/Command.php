<?php

declare(strict_types=1);

namespace App\Subscription\Command\ConfirmPayment;

final readonly class Command
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        public string $externalId
    ) {}
}
