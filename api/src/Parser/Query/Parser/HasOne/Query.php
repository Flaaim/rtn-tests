<?php

declare(strict_types=1);

namespace App\Parser\Query\Parser\HasOne;

final class Query
{
    public function __construct(
        public string $parserId,
    ) {}
}
