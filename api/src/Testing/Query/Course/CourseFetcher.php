<?php

declare(strict_types=1);

namespace App\Testing\Query\Course;

use Doctrine\DBAL\Connection;

/** @psalm-suppress UnusedClass */
final class CourseFetcher implements CourseFetcherInterface
{
    public function __construct(
        private readonly Connection $connection
    ) {}

    public function findAll(): array
    {
        $qb = $this->connection->createQueryBuilder();

        $result = $qb->select('c.course_id, c.status, c.name, c.created_at, c.cipher')
            ->from('courses', 'c')
            ->orderBy('c.course_id', 'ASC')
            ->executeQuery();

        return $result->fetchAllAssociative();
    }
}
