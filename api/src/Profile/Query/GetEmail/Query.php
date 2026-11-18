<?php

declare(strict_types=1);

namespace App\Profile\Query\GetEmail;

final readonly class Query
{
    public function __construct(
        public string $id
    ) {}
}
