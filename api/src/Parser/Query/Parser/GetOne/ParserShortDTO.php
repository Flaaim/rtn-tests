<?php

declare(strict_types=1);

namespace App\Parser\Query\Parser\GetOne;

final class ParserShortDTO
{
    public function __construct(
        public string $id,
        public readonly string $cookie,
        public readonly string $host
    ) {}
}
