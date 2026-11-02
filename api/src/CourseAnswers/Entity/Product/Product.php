<?php

declare(strict_types=1);

namespace App\CourseAnswers\Entity\Product;

use App\CourseAnswers\Entity\Payment\Amount;
use DateTimeImmutable;

final class Product
{
    public function __construct(
        private ProductId $id,
        private string $cipher,
        private Amount $amount,
        private string $filePath,
        private DateTimeImmutable $addedAt,
    ) {}

    public function getId(): ProductId
    {
        return $this->id;
    }

    public function getCipher(): string
    {
        return $this->cipher;
    }

    public function getAmount(): Amount
    {
        return $this->amount;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function getAddedAt(): DateTimeImmutable
    {
        return $this->addedAt;
    }
}
