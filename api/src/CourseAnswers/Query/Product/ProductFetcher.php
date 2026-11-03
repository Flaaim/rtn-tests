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

    public function getPaginated(int $page = 1, int $limit = 15, ?string $search = null): array
    {
        $page = max(1, $page);
        $limit = min(max(1, $limit), 100);
        $offset = ($page - 1) * $limit;

        $qb = $this->connection->createQueryBuilder();

        $qb->select('p.product_id, p.cipher, p.amount, p.currency, p.added_at')
            ->from('products', 'p');

        $normalizedSearch = null !== $search ? trim($search) : '';

        if (null !== $search && '' !== trim($search)) {
            $qb->andWhere($qb->expr()->eq('p.cipher', ':search'))
                ->setParameter('search', '%' . $normalizedSearch . '%');
        }

        $countQb = clone $qb;

        $totalCount = (int)$countQb->select('COUNT(p.product_id)')
            ->executeQuery()
            ->fetchOne();

        $rows = $qb->select('p.product_id, p.cipher, p.amount, p.currency, p.added_at')
            ->orderBy('p.added_at', 'DESC')
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
