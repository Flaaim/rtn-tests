<?php

declare(strict_types=1);

namespace App\Testing\Command\Attempt\LaunchMistakes;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class Command
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $userId,
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $testId
    ) {}
}
