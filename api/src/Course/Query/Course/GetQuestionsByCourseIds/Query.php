<?php

declare(strict_types=1);

namespace App\Course\Query\Course\GetQuestionsByCourseIds;

use Symfony\Component\Validator\Constraints as Assert;

final class Query
{
    public function __construct(
        #[Assert\Count(min: 1)]
        #[Assert\All(
            new Assert\Uuid()
        )]
        public readonly array $courseIds,
    ) {}
}
