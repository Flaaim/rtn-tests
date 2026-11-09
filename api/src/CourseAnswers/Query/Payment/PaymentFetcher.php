<?php

declare(strict_types=1);

namespace App\CourseAnswers\Query\Payment;

use Doctrine\DBAL\Connection;

final readonly class PaymentFetcher implements PaymentFetcherInterface
{
    public function __construct(
        private Connection $connection
    ) {}

    public function getResult(string $paymentId): array
    {
        $qb = $this->connection->createQueryBuilder();

        $result = $qb->select('
        p.status,
        p.email
        ')
            ->from('product_payments', 'p')
            ->where('p.payment_id = :id')
            ->setParameter('id', $paymentId)
            ->executeQuery();

        $row = $result->fetchAssociative();

        if (false === $row) {
            return [];
        }

        return $row;
    }
}
