<?php

declare(strict_types=1);

namespace App\CourseAnswers\Query\Payment\GetResult;

use App\CourseAnswers\Query\Payment\PaymentFetcherInterface;
use DomainException;

final readonly class QueryHandler
{
    public function __construct(
        private PaymentFetcherInterface $payments,
    ) {}

    public function handle(Query $query): PaymentDTO
    {
        $row = $this->payments->getResult($query->paymentId);

        if (empty($row)) {
            throw new DomainException('Payment result not found.');
        }

        return PaymentDTO::fromArray($row);
    }
}
