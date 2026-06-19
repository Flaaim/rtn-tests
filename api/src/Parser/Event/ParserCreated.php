<?php

declare(strict_types=1);

namespace App\Parser\Event;

final class ParserCreated
{
    public function __construct(
        public string $host,
    ) {}
}
