<?php

declare(strict_types=1);

namespace App\Course\Query\Course\GetQuestionsSitemap;

use App\Course\Query\Course\CourseFetcherInterface;

final readonly class QueryHandler
{
    public function __construct(
        private CourseFetcherInterface $courses
    ) {}

    public function handle(): array
    {
        return $this->courses->getAllQuestionIdsForSitemap();
    }
}
