<?php

declare(strict_types=1);

namespace App\Testing\Query\Test;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

/** @psalm-suppress UnusedClass */
final class TestFetcher implements TestFetcherInterface
{
    public function __construct(
        private readonly Connection $connection
    ) {}

    /**
     * @return array{items: list<array<string, mixed>>, totalCount: int}
     * @throws Exception
     */
    public function getPaginated(int $page = 1, int $limit = 15, ?string $search = null): array
    {
        $page = max(1, $page);
        $limit = min(max(1, $limit), 100);
        $offset = ($page - 1) * $limit;

        $qb = $this->connection->createQueryBuilder();

        $qb->select('t.id, t.status, t.name, t.created_at, t.cipher')
            ->from('tests', 't');

        $normalizedSearch = null !== $search ? trim($search) : '';

        if (null !== $search && '' !== trim($search)) {
            $qb->andWhere($qb->expr()->or(
                $qb->expr()->like('t.name', ':search'),
                $qb->expr()->like('t.cipher', ':search')
            ))
                ->setParameter('search', '%' . $normalizedSearch . '%');
        }

        $countQb = clone $qb;
        $totalCount = (int)$countQb->select('COUNT(t.id)')
            ->executeQuery()
            ->fetchOne();

        $rows = $qb->select('t.id, t.status, t.name, t.created_at, t.cipher')
            ->orderBy('t.name', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->executeQuery()
            ->fetchAllAssociative();

        return [
            'items' => $rows,
            'totalCount' => $totalCount,
        ];
    }
}
