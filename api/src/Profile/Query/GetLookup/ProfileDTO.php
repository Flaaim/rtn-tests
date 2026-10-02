<?php

declare(strict_types=1);

namespace App\Profile\Query\GetLookup;

final readonly class ProfileDTO
{
    public function __construct(
        public string $id,
        public string $email,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            email: $data['email'],
        );
    }
}
