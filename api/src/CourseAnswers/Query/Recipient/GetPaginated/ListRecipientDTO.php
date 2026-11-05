<?php

declare(strict_types=1);

namespace App\CourseAnswers\Query\Recipient\GetPaginated;

final class ListRecipientDTO
{
    public function __construct(
        /** @var RecipientDTO[] $items */
        public array $items,
        public int $totalCount,
        public int $totalPages,
    ) {}
}
