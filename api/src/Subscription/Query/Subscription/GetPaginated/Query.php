<?php

declare(strict_types=1);

namespace App\Subscription\Query\Subscription\GetPaginated;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class Query
{
    public function __construct(
        #[Assert\GreaterThan(0)]
        public int $page,
        #[Assert\GreaterThan(0)]
        public int $limit,
        public ?string $search = null,
    ) {}
}
