<?php

declare(strict_types=1);

namespace App\Course\Query\Course\GetPaginated;

use App\Course\Query\Course\CourseFetcherInterface;

final class QueryHandler
{
    public function __construct(
        private readonly CourseFetcherInterface $courses,
    ) {}

    public function handle(Query $query): ListCourseDTO
    {
        $safeLimit = max(1, $query->limit);

        $result = $this->courses->getPaginated(
            $query->page,
            $query->limit,
            $query->search
        );

        $items = array_map(
            static fn (array $row) => CourseDTO::fromArray($row),
            $result['items']
        );

        $totalCount = $result['totalCount'];

        $totalPages = $totalCount > 0 ? (int)ceil($totalCount / $safeLimit) : 0;

        return new ListCourseDTO(
            items: $items,
            totalCount: $totalCount,
            totalPages: $totalPages,
        );
    }
}
