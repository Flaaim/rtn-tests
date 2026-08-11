<?php

declare(strict_types=1);

namespace App\Course\Query\Course\GetOne;

use App\Course\Query\Course\CourseFetcherInterface;
use DomainException;

/** @psalm-suppress UnusedClass */
final class QueryHandler
{
    public function __construct(
        private readonly CourseFetcherInterface $fetcher,
    ) {}

    public function handle(Query $query): CourseFullDTO
    {
        $row = $this->fetcher->getOneById($query->id);

        if (empty($row)) {
            throw new DomainException('Course not found.');
        }

        return CourseFullDTO::fromArray($row);
    }
}
