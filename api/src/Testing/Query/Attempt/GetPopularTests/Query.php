<?php

declare(strict_types=1);

namespace App\Testing\Query\Attempt\GetPopularTests;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class Query
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $userId
    ) {}
}
