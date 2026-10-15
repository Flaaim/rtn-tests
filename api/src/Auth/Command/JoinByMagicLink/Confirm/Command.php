<?php

declare(strict_types=1);

namespace App\Auth\Command\JoinByMagicLink\Confirm;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class Command
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $token,
        #[Assert\Uuid]
        public ?string $guestId = null,
    ) {}
}
