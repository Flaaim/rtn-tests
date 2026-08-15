<?php

declare(strict_types=1);

namespace App\Course\Query\Course\GetLookup;

use App\Course\Query\Course\CourseFetcherInterface;

final class QueryHandler
{
    public function __construct(
        private readonly CourseFetcherInterface $courses
    ) {}

    public function handle(): array
    {
        $rows = $this->courses->getLookupList();

        if (empty($rows)) {
            return [];
        }

        return array_map(
            static fn (array $row): CourseDTO => CourseDTO::fromArray($row),
            $rows
        );
    }
}
