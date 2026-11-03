<?php

declare(strict_types=1);

namespace App\CourseAnswers\Test\Builder;

use App\CourseAnswers\Entity\Payment\Amount;
use App\CourseAnswers\Entity\Product\Product;
use App\CourseAnswers\Entity\Product\ProductId;
use DateTimeImmutable;

final class ProductBuilder
{
    private ProductId $productId;
    private string $cipher;
    private string $courseId;
    private Amount $amount;
    private string $filePath;
    private DateTimeImmutable $addedAt;

    public function __construct(
    ) {
        $this->productId = new ProductId('61b137e9-12d8-4260-b1ae-5f643e6fcc60');
        $this->cipher = 'ОТ 201.18';
        $this->courseId = 'd4180097-ef4e-47d7-b4da-4e29c1d8ce7b';
        $this->amount = new Amount('450.00', 'RUB');
        $this->addedAt = new DateTimeImmutable('now');
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function withProductId(ProductId $productId): self
    {
        $clone = clone $this;
        $clone->productId = $productId;
        return $clone;
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function withCipher(string $cipher): self
    {
        $clone = clone $this;
        $clone->cipher = $cipher;
        return $clone;
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function withCourseId(string $courseId): self
    {
        $clone = clone $this;
        $clone->courseId = $courseId;
        return $clone;
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function withAmount(Amount $amount): self
    {
        $clone = clone $this;
        $clone->amount = $amount;
        return $clone;
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function withFilePath(string $filePath): self
    {
        $clone = clone $this;
        $clone->filePath = $filePath;
        return $clone;
    }

    public function build(): Product
    {
        return new Product(
            $this->productId,
            $this->cipher,
            $this->courseId,
            $this->amount,
            $this->filePath,
            $this->addedAt
        );
    }
}
