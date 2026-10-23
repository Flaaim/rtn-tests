<?php

declare(strict_types=1);

namespace App\Profile\Api;

use App\Profile\Query\Get\Query;
use App\Profile\Query\Get\QueryHandler;
use App\Subscription\Interfaces\DTO\ProfileDTO;
use App\Subscription\Interfaces\ProfileInterface;

final readonly class ProfileApi implements ProfileInterface
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private QueryHandler $queryHandler,
    ) {}

    public function getDetail(string $userId): ProfileDTO
    {
        $profileDTO = $this->queryHandler->handle(new Query($userId));

        return new ProfileDTO(
            id: $profileDTO->id,
            email: $profileDTO->email,
            role: $profileDTO->role,
            status: $profileDTO->status,
            date: $profileDTO->date,
            networks: $profileDTO->networks,
        );
    }
}
