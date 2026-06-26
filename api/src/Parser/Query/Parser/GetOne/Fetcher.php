<?php

declare(strict_types=1);

namespace App\Parser\Query\Parser\GetOne;

use App\Parser\Query\Parser\ParserFetcherInterface;
use Doctrine\DBAL\Connection;

final class Fetcher
{
    public function __construct(
      private readonly ParserFetcherInterface $fetcher
    ) {}

    public function fetch(Query $query): ParserShortDTO
    {
        $row = $this->fetcher->getOneById($query->parserId);

        if(empty($row)){
            throw new \DomainException('Parser not found.');
        }

        return new ParserShortDTO(
            $row['id'],
            $row['cookie'],
            $row['host'],
        );
    }
}
