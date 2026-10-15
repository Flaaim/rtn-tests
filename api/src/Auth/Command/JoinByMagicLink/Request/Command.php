<?php

declare(strict_types=1);

namespace App\Auth\Command\JoinByMagicLink\Request;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class Command
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $email
    ) {}
}
