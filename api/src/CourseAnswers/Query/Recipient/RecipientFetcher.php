<?php

declare(strict_types=1);

namespace App\CourseAnswers\Query\Recipient;

use Doctrine\DBAL\Connection;

final readonly class RecipientFetcher implements RecipientFetcherInterface
{
    public function __construct(
        private Connection $connection,
    ) {}

    public function getOneById(string $id): array
    {
        $qb = $this->connection->createQueryBuilder();

        $result = $qb->select(
            '
            r.recipient_id,
            r.email,
            r.status,
            r.send_at,
            r.created_at,
            r.failed_reason,
            p.cipher,
            p.product_id'
        )
            ->from('product_recipients', 'r')
            ->join('r', 'products', 'p', 'p.product_id = r.product_id')
            ->where('r.recipient_id = :id')
            ->setParameter('id', $id)
            ->orderBy('r.created_at', 'DESC')
            ->executeQuery();

        $row = $result->fetchAssociative();
        if (false === $row) {
            return [];
        }

        return [
            'recipientId' => $row['recipient_id'],
            'email' => $row['email'],
            'status' => $row['status'],
            'sendAt' => $row['send_at'],
            'createdAt' => $row['created_at'],
            'failedReason' => $row['failed_reason'],
            'product' => [
                'productId' => $row['product_id'],
                'cipher' => $row['cipher'],
            ],
        ];
    }

    public function getPaginated(int $page = 1, int $limit = 15, ?string $search = null): array
    {
        $page = max(1, $page);
        $limit = min(max(1, $limit), 100);
        $offset = ($page - 1) * $limit;

        $qb = $this->connection->createQueryBuilder();

        $qb->select('r.recipient_id, r.status, r.email, r.created_at')
            ->from('product_recipients', 'r');

        $normalizedSearch = null !== $search ? trim($search) : '';

        if (null !== $search && '' !== trim($search)) {
            $qb->andWhere($qb->expr()->eq('r.email', ':search'))
                ->setParameter('search', '%' . $normalizedSearch . '%');
        }

        $countQb = clone $qb;

        $totalCount = (int)$countQb->select('COUNT(r.recipient_id)')
            ->executeQuery()
            ->fetchOne();

        $rows = $qb->select('r.recipient_id, r.status, r.email, r.created_at')
            ->orderBy('r.created_at', 'DESC')
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
