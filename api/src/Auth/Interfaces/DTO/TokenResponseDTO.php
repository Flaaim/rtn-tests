<?php

declare(strict_types=1);

namespace App\Auth\Interfaces\DTO;

final readonly class TokenResponseDTO
{
    public function __construct(
        public string $access_token,
        public string $refresh_token,
        public int $expires_in,
    ) {}
}
