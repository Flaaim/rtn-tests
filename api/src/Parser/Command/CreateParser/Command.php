<?php

declare(strict_types=1);

namespace App\Parser\Command\CreateParser;

final class Command
{
    public function __construct(
        public readonly string $host,
        public readonly array $data,
        public readonly array $options = []
    ) {
    }
}
