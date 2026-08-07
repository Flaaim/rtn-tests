<?php

declare(strict_types=1);

namespace App\Testing\Query\Course;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

/** @psalm-suppress UnusedClass */
final class CourseFetcher implements CourseFetcherInterface
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

        $qb->select('c.course_id, c.status, c.name, c.created_at, c.cipher')
            ->from('courses', 'c');

        $normalizedSearch = null !== $search ? trim($search) : '';

        if (null !== $search && '' !== trim($search)) {
            $qb->andWhere($qb->expr()->or(
                $qb->expr()->like('c.name', ':search'),
                $qb->expr()->like('c.cipher', ':search')
            ))
                ->setParameter('search', '%' . $normalizedSearch . '%');
        }

        $countQb = clone $qb;
        $totalCount = (int)$countQb->select('COUNT(c.course_id)')
            ->executeQuery()
            ->fetchOne();

        $rows = $qb->select('c.course_id, c.status, c.name, c.created_at, c.cipher')
            ->orderBy('c.name', 'ASC')
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
