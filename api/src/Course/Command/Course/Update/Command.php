<?php

declare(strict_types=1);

namespace App\Course\Command\Course\Update;

use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $id,
        #[Assert\NotBlank]
        public string $draft
    ) {}
}
