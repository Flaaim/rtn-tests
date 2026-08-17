<?php

declare(strict_types=1);

namespace App\Testing\Command\Test\Rename;

use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $id,
        #[Assert\NotBlank]
        public string $name,
        #[Assert\NotBlank]
        public string $description,
    ) {}
}
