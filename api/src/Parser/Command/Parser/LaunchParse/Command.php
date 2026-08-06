<?php

declare(strict_types=1);

namespace App\Parser\Command\Parser\LaunchParse;

use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $parserId,
        #[Assert\NotBlank]
        public string $branchId,
        #[Assert\NotBlank]
        public string $ticketId
    ) {}
}
