<?php

declare(strict_types=1);

namespace App\Parser\Query\Parser\GetParsers;

use App\Parser\Query\Parser\ParserFetcherInterface;
use DomainException;

final class QueryHandler
{
    public function __construct(
        private readonly ParserFetcherInterface $parsers,
    ) {}

    /** @return ParserShortDTO[] */
    public function handle(): array
    {
        $rows = $this->parsers->findAll();

        if (empty($rows)) {
            throw new DomainException('No parsers has been found.');
        }

        return array_map(static fn (array $row): ParserShortDTO => ParserShortDTO::fromArray($row), $rows);
    }
}
