<?php

declare(strict_types=1);

namespace App\Testing\Query\Course\GetOne;

final class Query
{
    public function __construct(
        public string $id,
    ) {}
}
