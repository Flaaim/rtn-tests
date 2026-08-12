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

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getCourseId(): string
    {
        return $this->courseId;
    }

    public function getAllowedMistakes(): int
    {
        return $this->allowedMistakes;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTickets(): array
    {
        return $this->tickets;
    }

    public function getQuestions(): array
    {
        return $this->questions;
    }

    public function getCipher(): string
    {
        return $this->cipher;
    }
}
