<?php

declare(strict_types=1);

namespace App\Testing\Entity;

use App\SharedDomain\AggregateRoot;
use App\SharedDomain\Event\EventTrait;
use App\Testing\Entity\DTO\TicketDTO;
use App\Testing\Event\TestCreated;
use DateTimeImmutable;
use InvalidArgumentException;

final class Test implements AggregateRoot
{
    use EventTrait;

    /** @param TicketDTO[] $tickets */
    public function __construct(
        private TestId $id,
        private string $name,
        private string $cipher,
        private string $description,
        private int $allowedMistakes,
        private string $courseId,
        private array $tickets,
        private Status $status,
        private string $slug,
        private DateTimeImmutable $createdAt,
    ) {
        foreach ($this->tickets as $ticket) {
            if (!$ticket instanceof TicketDTO) {
                throw new InvalidArgumentException('Ticket should be an instance of ' . TicketDTO::class);
            }
        }

        $this->recordEvent(new TestCreated($id->getValue()));
    }

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

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getCipher(): string
    {
        return $this->cipher;
    }

    public function getSequentialQuestions(): array
    {
        $allQuestions = [];
        /** @var TicketDTO $ticket */
        foreach ($this->tickets as $ticket) {
            $allQuestions = array_merge($allQuestions, $ticket->questionIds);
        }

        return array_values(array_unique($allQuestions));
    }
}
