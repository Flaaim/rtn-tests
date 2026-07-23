<?php

declare(strict_types=1);

namespace App\Parser\Query\Parser;

use Doctrine\DBAL\Connection;

final class ParserFetcher implements ParserFetcherInterface
{
    public function __construct(
        private readonly Connection $connection,
    ) {}

    public function getOneById(string $id): array
    {
        $qb = $this->connection->createQueryBuilder();

        $result = $qb->select('p.id, p.cookie, p.host')
            ->from('parsers', 'p')
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->executeQuery();

        $result = $result->fetchAssociative();

        if (false === $result) {
            return [];
        }
        return $result;
    }

    public function hasOneById(string $id): bool
    {
        $qb = $this->connection->createQueryBuilder();

        $result = $qb->select('1')
            ->from('parsers', 'p')
            ->where($qb->expr()->eq('p.id', ':id'))
            ->setParameter('id', $id)
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchOne();

        return false !== $result;
    }
}
