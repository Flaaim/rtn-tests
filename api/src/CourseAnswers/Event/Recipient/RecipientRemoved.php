<?php

declare(strict_types=1);

namespace App\CourseAnswers\Event\Recipient;

final readonly class RecipientRemoved
{
    public function __construct(
        public string $recipientId
    ) {}
}
