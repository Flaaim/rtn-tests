<?php

declare(strict_types=1);

namespace App\CourseAnswers\Query\Recipient\GetOne;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class Query
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $id,
    ) {}
}
