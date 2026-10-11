<?php

declare(strict_types=1);

namespace App\Testing\Command\Attempt\Launch;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class Command
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $testId,
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $userId,
        #[Assert\GreaterThan(0)]
        public int $ticketNumber
    ) {}
}
