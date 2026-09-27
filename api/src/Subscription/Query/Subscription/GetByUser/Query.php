<?php

declare(strict_types=1);

namespace App\Subscription\Query\Subscription\GetByUser;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class Query
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $userId,
        #[Assert\GreaterThan(0)]
        public int $page = 1,
        #[Assert\GreaterThan(0)]
        public int $limit = 15,
    ) {}
}
