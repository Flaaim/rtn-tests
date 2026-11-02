<?php

declare(strict_types=1);

namespace App\CourseAnswers\Entity\Product;

use App\CourseAnswers\Entity\Payment\Amount;

final class Product
{
    public function __construct(
        private ProductId $id,
        private string $cipher,
        private Amount $amount,
        private string $filePath
    ) {}

    public function getId(): ProductId
    {
        return $this->id;
    }

    public function getCipher(): string
    {
        return $this->cipher;
    }
}
