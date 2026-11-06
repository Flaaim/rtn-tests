<?php

declare(strict_types=1);

namespace App\CourseAnswers\Entity\Product;

use App\CourseAnswers\Entity\Payment\Amount;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'products')]
final class Product
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(name: 'product_id', type: 'product_id', unique: true)]
        private ProductId $productId,
        #[ORM\Column(type: 'string', length: 25)]
        private string $cipher,
        #[ORM\Column(type: 'string', length: 255, unique: true)]
        private string $courseId,
        #[ORM\Embedded(class: Amount::class, columnPrefix: false)]
        private Amount $amount,
        #[ORM\Column(type: 'string', length: 255)]
        private string $filePath,
        #[ORM\Column(type: 'datetime_immutable')]
        private DateTimeImmutable $addedAt,
    ) {}

    public function getId(): ProductId
    {
        return $this->productId;
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

    public function getCourseId(): string
    {
        return $this->courseId;
    }

    public function edit(string $cipher, Amount $amount, string $filePath, DateTimeImmutable $addedAt): void
    {
        $this->cipher = $cipher;
        $this->amount = $amount;
        $this->filePath = $filePath;
        $this->addedAt = $addedAt;
    }

    public function changeAmount(Amount $amount): void
    {
        $this->amount = $amount;
    }
}
