<?php

declare(strict_types=1);

namespace App\Subscription\Interfaces\DTO;

final readonly class ProfileDTO
{
    public function __construct(
        public string $id,
        public string $email,
        public string $role,
        public string $status,
        public string $date,
        public array $networks = [],
    ) {}
}
