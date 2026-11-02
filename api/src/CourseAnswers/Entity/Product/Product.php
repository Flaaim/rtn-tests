<?php

declare(strict_types=1);

namespace App\CourseAnswers\Entity\Product;

use DateTimeImmutable;

final class Product
{
    public function __construct(
        private ProductId $id,
        private string $cipher,
        private string $paymentId,
        private Status $status,
        private DateTimeImmutable $sentAt,
    ) {}

    public function getId(): ProductId
    {
        return $this->id;
    }

    public function getCipher(): string
    {
        return $this->cipher;
    }

    public function getPaymentId(): string
    {
        return $this->paymentId;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function getSentAt(): DateTimeImmutable
    {
        return $this->sentAt;
    }
}
