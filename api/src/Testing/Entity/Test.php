<?php

declare(strict_types=1);

namespace App\Testing\Entity;

final class Test
{
    public function __construct(
        private TestId $id,
        private string $name,
        private string $cipher,
        private string $description,
        private int $allowedMistakes,
        private string $courseId,
        private array $tickets,
        private array $questions,
        private Status $status,
        private string $slug,
    ) {}

    public function getId(): TestId
    {
        return $this->id;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }
}
