<?php

declare(strict_types=1);

namespace App\CourseAnswers\Query\Product\GetOne;

use Symfony\Component\Validator\Constraints as Assert;

final class Query
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $id,
    ) {}
}
