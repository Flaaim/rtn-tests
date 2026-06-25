<?php

declare(strict_types=1);

namespace App\Parser\Query\Task\Get;

final class Query
{
    public function __construct(
        public string $taskId
    ) {}
}
