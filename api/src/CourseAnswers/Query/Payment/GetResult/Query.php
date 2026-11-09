<?php

declare(strict_types=1);

namespace App\CourseAnswers\Query\Payment\GetResult;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class Query
{
    public function __construct(
        #[Assert\Uuid]
        public string $paymentId,
    ) {}
}
