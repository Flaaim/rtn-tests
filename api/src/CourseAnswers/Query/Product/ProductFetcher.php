<?php

declare(strict_types=1);

namespace App\CourseAnswers\Query\Product;

use Doctrine\DBAL\Connection;

final readonly class ProductFetcher implements ProductFetcherInterface
{
    public function __construct(
        private Connection $connection,
    ) {}

    public function getOneById(string $id): array
    {
        $qb = $this->connection->createQueryBuilder();
        $result = $qb->select(
            '
            p.product_id,
            p.cipher,
            p.course_id,
            p.amount,
            p.currency,
            p.file_path,
            p.added_at'
        )
            ->from('products', 'p')
            ->where('p.product_id = :id')
            ->setParameter('id', $id)
            ->executeQuery();

        $row = $result->fetchAssociative();

        if (false === $row) {
            return [];
        }

        return $row;
    }
}
