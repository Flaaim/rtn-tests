<?php

declare(strict_types=1);

namespace App\Course\Command\Course\Add;

use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    public function __construct(
        #[Assert\NotBlank]
        public string $name,
        #[Assert\NotBlank]
        public string $draft,
        #[Assert\NotBlank]
        public string $cipher
    ) {}
}
