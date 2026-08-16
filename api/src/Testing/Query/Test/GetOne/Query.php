<?php

declare(strict_types=1);

namespace App\Testing\Query\Test\GetOne;

final class Query
{
    public function __construct(
        public readonly string $id,
    ) {}
}
