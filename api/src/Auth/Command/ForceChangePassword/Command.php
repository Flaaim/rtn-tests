<?php

declare(strict_types=1);

namespace App\Auth\Command\ForceChangePassword;

final readonly class Command
{
    public function __construct(
        public string $id,
        public string $password,
    ) {}
}
