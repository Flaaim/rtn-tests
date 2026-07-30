<?php

declare(strict_types=1);

namespace App\Parser\Query\Parser\GetParsers;

final class ParserShortDTO
{
    public function __construct(
        public string $id,
        public string $host,
    ) {}

    public static function fromArray(array $array): self
    {
        return new self(
            id: $array['id'],
            host: $array['host'],
        );
    }
}
