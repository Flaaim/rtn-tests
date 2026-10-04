<?php

declare(strict_types=1);

namespace App\Subscription\Command\Remove;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class Command
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $id,
    ) {}
}
