<?php

declare(strict_types=1);

namespace App\Testing\Test\Builder;

use App\Testing\Entity\Test\DTO\TicketDTO;
use App\Testing\Entity\Test\Test;
use App\Testing\Entity\Test\TestId;
use DateTimeImmutable;

final class TestBuilder
{
    private TestId $id;
    private string $name;
    private string $description;
    private string $cipher;
    private int $allowedMistakes;
    private array $courseIds;
    private array $tickets;
    private string $slug;
    private DateTimeImmutable $createdAt;
    private bool $active = false;

    public function __construct(
    ) {
        $this->id = new TestId('6ed7c3cb-b8ea-4615-8cfe-67b389a2d193');
        $this->name = 'Первая помощь';
        $this->cipher = 'ОТ 201.18';
        $this->description = 'Test description';
        $this->allowedMistakes = 2;
        $this->courseIds = ['0121b081-c461-42f0-b8ec-a4632a64faea'];
        $this->tickets = [new TicketDTO(1, ['7645fc15-26aa-4c3c-a5a4-9724c9f5f455', '48b75db2-113c-4ae7-becb-7bc830016c61'])];
        $this->slug = 'ot201';
        $this->createdAt = new DateTimeImmutable();
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function withId(TestId $id): self
    {
        $clone = clone $this;
        $clone->id = $id;
        return $clone;
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function withName(string $name): self
    {
        $clone = clone $this;
        $clone->name = $name;
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
    public function withDescription(string $description): self
    {
        $clone = clone $this;
        $clone->description = $description;
        return $clone;
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function withAllowedMistakes(int $allowedMistakes): self
    {
        $clone = clone $this;
        $clone->allowedMistakes = $allowedMistakes;
        return $clone;
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function withCourseIds(array $courseIds): self
    {
        $clone = clone $this;
        $clone->courseIds = $courseIds;
        return $clone;
    }

    /** @param TicketDTO[] $tickets */
    public function withTickets(array $tickets): self
    {
        $clone = clone $this;
        $clone->tickets = $tickets;
        return $clone;
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function withSlug(string $slug): self
    {
        $clone = clone $this;
        $clone->slug = $slug;
        return $clone;
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function active(): self
    {
        $clone = clone $this;
        $clone->active = true;
        return $clone;
    }

    public function build(): Test
    {
        $test = new Test(
            $this->id,
            $this->name,
            $this->cipher,
            $this->description,
            $this->allowedMistakes,
            $this->courseIds,
            $this->tickets,
            $this->slug,
            $this->createdAt
        );

        if ($this->active) {
            $test->activate();
        }

        return $test;
    }
}
