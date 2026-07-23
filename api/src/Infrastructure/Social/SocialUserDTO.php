<?php

declare(strict_types=1);

namespace App\Infrastructure\Social;

final class SocialUserDTO
{
    public string $email;

    public function __construct(
        public string $identity,
        public string $network,
        ?string $email,
    ) {
        $this->email = $email ?? \sprintf('%s@%s.local', $identity, $network);
    }
}
