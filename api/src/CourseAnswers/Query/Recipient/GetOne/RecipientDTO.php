<?php

declare(strict_types=1);

namespace App\CourseAnswers\Query\Recipient\GetOne;

final readonly class RecipientDTO
{
    public function __construct(
        public string $recipientId,
        public string $email,
        public string $status,
        public ProductDTO $product,
        public string $createdAt,
        public ?string $failedReason = null,
        public ?string $sendAt = null,
    ) {}

    public static function fromArray(array $data): self
    {
        $product = ProductDTO::fromArray($data['product']);

        return new self(
            recipientId: $data['recipientId'],
            email: $data['email'],
            status: $data['status'],
            product: $product,
            createdAt: $data['createdAt'],
            failedReason: $data['failedReason'],
            sendAt: $data['sendAt'],
        );
    }
}
