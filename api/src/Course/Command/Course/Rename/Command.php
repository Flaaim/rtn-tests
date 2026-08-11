<?php

declare(strict_types=1);

namespace App\Course\Command\Course\Rename;

use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public readonly string $id,
        #[Assert\NotBlank]
        public readonly string $name,
        #[Assert\NotBlank]
        public readonly string $cipher
    ) {}
}
