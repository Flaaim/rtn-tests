<?php

declare(strict_types=1);

namespace App\OAuth\Api;

use App\Auth\Interfaces\DTO\TokenResponseDTO;
use App\Auth\Interfaces\TokenGeneratorInterface;
use App\OAuth\Service\ManualTokenGenerator;

final readonly class TokenApi implements TokenGeneratorInterface
{
    public function __construct(
        private ManualTokenGenerator $manualTokenGenerator,
    ) {}

    public function generateTokenForUser(string $userId): TokenResponseDTO
    {
        $result = $this->manualTokenGenerator->generateForUser($userId);

        return new TokenResponseDTO(
            access_token: $result['access_token'],
            refresh_token: $result['refresh_token'],
            expires_in: $result['expires_in'],
        );
    }
}
