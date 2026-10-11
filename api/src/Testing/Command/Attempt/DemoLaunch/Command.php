<?php

declare(strict_types=1);

namespace App\Testing\Command\Attempt\DemoLaunch;

use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $testId,
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $guestId,
        #[Assert\GreaterThan(0)]
        public int $ticketNumber
    ) {}
}
