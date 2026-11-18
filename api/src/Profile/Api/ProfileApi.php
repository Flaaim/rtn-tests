<?php

declare(strict_types=1);

namespace App\Profile\Api;

use App\Profile\Query\GetEmail\Query;
use App\Profile\Query\GetEmail\QueryHandler;

final readonly class ProfileApi
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private QueryHandler $queryHandler,
    ) {}

    public function getEmail(string $userId): ?string
    {
        return $this->queryHandler->handle(new Query($userId));
    }
}
