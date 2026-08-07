<?php

declare(strict_types=1);

namespace App\Testing\Query\Course\GetPaginated;

use Symfony\Component\Validator\Constraints as Assert;

final class Query
{
    public function __construct(
        #[Assert\GreaterThan(0)]
        public readonly int $page = 1,
        #[Assert\GreaterThan(0)]
        public readonly int $limit = 15,
        public readonly ?string $search = null,
    ) {}
}
