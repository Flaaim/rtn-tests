<?php

declare(strict_types=1);

namespace App\Auth\Interfaces;

use App\Auth\Interfaces\DTO\TokenResponseDTO;

interface TokenGeneratorInterface
{
    public function generateTokenForUser(string $userId): TokenResponseDTO;
}
