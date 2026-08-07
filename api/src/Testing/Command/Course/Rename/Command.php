<?php

declare(strict_types=1);

namespace App\Testing\Command\Course\Rename;

final class Command
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $cipher
    ) {}
}
