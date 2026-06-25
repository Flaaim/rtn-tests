<?php

declare(strict_types=1);

namespace App\Parser\Event;

final class ParseFailed
{
    public function __construct(
        public string $parseId,
        public string $reason
    ) {}
}
