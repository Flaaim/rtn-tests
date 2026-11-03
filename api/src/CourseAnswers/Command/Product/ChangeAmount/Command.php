<?php

declare(strict_types=1);

namespace App\CourseAnswers\Command\Product\ChangeAmount;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class Command
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $productId,
        #[Assert\NotBlank]
        public string $amount,
    ) {}
}
