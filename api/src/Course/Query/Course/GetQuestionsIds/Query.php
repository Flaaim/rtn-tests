<?php

declare(strict_types=1);

namespace App\Course\Query\Course\GetQuestionsIds;

final class Query
{
    public function __construct(
        public readonly string $id
    ) {}
}
