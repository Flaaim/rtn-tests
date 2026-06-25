<?php

declare(strict_types=1);

namespace App\Parser\Query\Parser\Get;

use Symfony\Component\Validator\Constraints as Assert;

final class Query
{
    public function __construct(
        #[Assert\Uuid]
        #[Assert\NotBlank]
        public string $parserId
    ) {}
}
