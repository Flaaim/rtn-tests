<?php

declare(strict_types=1);

namespace App\Subscription\Query\Subscription\GetLatestByUser;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class Query
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $userId,
    ) {}
}
