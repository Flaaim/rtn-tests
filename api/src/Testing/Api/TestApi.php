<?php

declare(strict_types=1);

namespace App\Testing\Api;

use App\Testing\Query\Test\GetByCourseId\Query;
use App\Testing\Query\Test\GetByCourseId\QueryHandler;
use App\Testing\Query\Test\GetByCourseId\TestDTO;

final readonly class TestApi
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private QueryHandler $queryHandler,
    ) {}

    public function getTestByCourseId(string $courseId): ?TestDTO
    {
        return $this->queryHandler->handle(new Query($courseId));
    }
}
