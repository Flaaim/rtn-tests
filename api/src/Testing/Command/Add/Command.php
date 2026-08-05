<?php

declare(strict_types=1);

namespace App\Testing\Command\Add;

final class Command
{
    public function __construct(
        public string $name,
        public string $draft
    ) {}
}
