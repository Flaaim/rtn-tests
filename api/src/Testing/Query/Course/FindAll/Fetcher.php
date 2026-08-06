<?php

declare(strict_types=1);

namespace App\Testing\Query\Course\FindAll;

use App\Testing\Query\Course\CourseFetcherInterface;
use DomainException;

final class Fetcher
{
    public function __construct(
        private readonly CourseFetcherInterface $courses,
    ) {}

    /** @return CourseDTO[] */
    public function handle(): array
    {
        $rows = $this->courses->findAll();
        if (empty($rows)) {
            throw new DomainException('Courses not found.');
        }

        return array_map(static fn (array $row) => CourseDTO::fromArray($row), $rows);
    }
}
