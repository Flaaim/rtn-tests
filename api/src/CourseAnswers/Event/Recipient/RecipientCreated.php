<?php

declare(strict_types=1);

namespace App\CourseAnswers\Event\Recipient;

final readonly class RecipientCreated
{
    public function __construct(
        public string $recipientId,
        public string $productId,
        public string $email,
    ) {}
}
