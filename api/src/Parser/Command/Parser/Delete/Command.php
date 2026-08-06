<?php

declare(strict_types=1);

namespace App\Parser\Command\Parser\Delete;

final class Command
{
    public function __construct(
        public string $id
    ) {}
}
