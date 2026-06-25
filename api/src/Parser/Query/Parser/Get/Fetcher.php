<?php

declare(strict_types=1);

namespace App\Parser\Query\Parser\Get;

use Doctrine\DBAL\Connection;

final class Fetcher
{
    public function __construct(
      private readonly Connection $connection
    ) {}
    public function fetch(Query $query): ParserDTO
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->select('p.cookie, p.host')
            ->from('parsers', 'p')
            ->where('p.id = :id')
            ->setParameter('id', $query->parserId)
            ->executeQuery();

        $result = $qb->fetchAssociative();

        if($result === false){
            throw new \DomainException('Parser not found.');
        }

        return new ParserDTO($result['cookie'], $result['host']);
    }
}
