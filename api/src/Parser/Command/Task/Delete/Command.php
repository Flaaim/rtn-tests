<?php

declare(strict_types=1);

namespace App\Parser\Command\Task\Delete;

use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\All([
            new Assert\Uuid(),
        ])]
        public array $ids,
    ) {}
}
