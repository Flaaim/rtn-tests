<?php

declare(strict_types=1);

namespace App\CourseAnswers\Entity\File;


final class File
{
    public function __construct(
        private FileId $id,
        private string $cipher,
        private string $paymentId,
        private Status $status,
        private \DateTimeImmutable $sentAt,
    ) {}
}
