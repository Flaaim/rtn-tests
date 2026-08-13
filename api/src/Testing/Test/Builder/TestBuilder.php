<?php

declare(strict_types=1);

namespace App\Testing\Test\Builder;

use App\Testing\Entity\DTO\TicketDTO;
use App\Testing\Entity\Status;
use App\Testing\Entity\Test;
use App\Testing\Entity\TestId;
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
    private Status $status;
    private string $slug;
    private DateTimeImmutable $createdAt;

    public function __construct(
    ) {
        $this->id = new TestId('6ed7c3cb-b8ea-4615-8cfe-67b389a2d193');
        $this->name = 'Первая помощь';
        $this->cipher = 'ОТ 201.18';
        $this->description = 'Test description';
        $this->allowedMistakes = 2;
        $this->courseIds = ['0121b081-c461-42f0-b8ec-a4632a64faea'];
        $this->tickets = [];
        $this->status = Status::inactive();
        $this->slug = 'ot201';
        $this->createdAt = new DateTimeImmutable();
    }

    public function withId(TestId $id): self
    {
        $clone = clone $this;
        $clone->id = $id;
        return $clone;
    }

    public function withName(string $name): self
    {
        $clone = clone $this;
        $clone->name = $name;
        return $clone;
    }

    public function withDescription(string $description): self
    {
        $clone = clone $this;
        $clone->description = $description;
        return $clone;
    }

    public function withAllowedMistakes(int $allowedMistakes): self
    {
        $clone = clone $this;
        $clone->allowedMistakes = $allowedMistakes;
        return $clone;
    }

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

    public function withStatus(Status $status): self
    {
        $clone = clone $this;
        $clone->status = $status;
        return $clone;
    }

    public function withSlug(string $slug): self
    {
        $clone = clone $this;
        $clone->slug = $slug;
        return $clone;
    }

    public function build(): Test
    {
        return new Test(
            $this->id,
            $this->name,
            $this->description,
            $this->cipher,
            $this->allowedMistakes,
            $this->courseIds,
            $this->tickets,
            $this->status,
            $this->slug,
            $this->createdAt
        );
    }
}
