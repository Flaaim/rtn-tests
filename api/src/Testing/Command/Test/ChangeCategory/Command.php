<?php

declare(strict_types=1);

namespace App\Testing\Command\Test\ChangeCategory;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class Command
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $testId,
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $categoryId,
    ) {}
}
