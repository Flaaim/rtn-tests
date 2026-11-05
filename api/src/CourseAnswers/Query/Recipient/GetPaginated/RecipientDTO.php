<?php

declare(strict_types=1);

namespace App\CourseAnswers\Query\Recipient\GetPaginated;

final readonly class RecipientDTO
{
    public function __construct(
        public string $recipientId,
        public string $status,
        public string $email,
        public string $createdAt,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            recipientId: $data['recipient_id'],
            status: $data['status'],
            email: $data['email'],
            createdAt: $data['created_at'],
        );
    }
}
