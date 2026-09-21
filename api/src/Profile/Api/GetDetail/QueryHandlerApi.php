<?php

declare(strict_types=1);

namespace App\Profile\Api\GetDetail;

use App\Profile\Query\Get\ProfileDTO;
use App\Profile\Query\Get\Query;
use App\Profile\Query\Get\QueryHandler;

final readonly class QueryHandlerApi
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private QueryHandler $queryHandler,
    ) {}

    public function getDetail(string $userId): ProfileDTO
    {
        return $this->queryHandler->handle(new Query($userId));
    }
}
