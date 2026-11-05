<?php

declare(strict_types=1);

namespace App\CourseAnswers\Query\Recipient\GetOne;

use App\CourseAnswers\Query\Recipient\RecipientFetcherInterface;
use DomainException;

final readonly class QueryHandler
{
    public function __construct(
        private RecipientFetcherInterface $recipients
    ) {}

    public function handle(Query $query): RecipientDTO
    {
        $recipient = $this->recipients->getOneById($query->id);

        if (empty($recipient)) {
            throw new DomainException('Recipient not found.');
        }
        return RecipientDTO::fromArray($recipient);
    }
}
