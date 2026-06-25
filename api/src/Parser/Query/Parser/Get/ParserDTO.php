<?php

declare(strict_types=1);

namespace App\Parser\Query\Parser\Get;

final class ParserDTO
{
    public function __construct(
        public readonly string $cookie,
        public readonly string $host
    ) {}
}
