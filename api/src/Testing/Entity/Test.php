<?php

declare(strict_types=1);

namespace App\Testing\Entity;

use App\SharedDomain\AggregateRoot;
use App\SharedDomain\Event\EventTrait;
use App\Testing\Entity\DTO\TicketDTO;
use App\Testing\Event\TestCreated;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use DomainException;
use InvalidArgumentException;

#[ORM\Entity]
#[ORM\Table(name: 'tests')]
final class Test implements AggregateRoot
{
    use EventTrait;
    #[ORM\Column(type: 'test_status')]
    private Status $status;

    /** @param TicketDTO[] $tickets */
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'test_id', unique: true)]
        private TestId $id,
        #[ORM\Column(type: 'string', length: 255)]
        private string $name,
        #[ORM\Column(type: 'string', length: 255)]
        private string $cipher,
        #[ORM\Column(type: 'string', length: 512)]
        private string $description,
        #[ORM\Column(type: 'integer')]
        private int $allowedMistakes,
        #[ORM\Column(type: Types::JSON, options: ['jsonb' => true])]
        private array $courseIds,
        #[ORM\Column(type: Types::JSON, options: ['jsonb' => true])]
        private array $tickets,
        #[ORM\Column(type: 'string', length: 255)]
        private string $slug,
        #[ORM\Column(type: 'datetime_immutable')]
        private DateTimeImmutable $createdAt,
    ) {
        foreach ($this->tickets as $ticket) {
            if (!$ticket instanceof TicketDTO) {
                throw new InvalidArgumentException('Ticket should be an instance of ' . TicketDTO::class);
            }
        }
        $this->status = Status::inactive();
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

    public function getCourseId(): array
    {
        return $this->courseIds;
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
        foreach ($this->tickets as $ticket) {
            $allQuestions = array_merge($allQuestions, $ticket->questionIds);
        }

        return array_values(array_unique($allQuestions));
    }

    public function activate(): void
    {
        if ($this->isActive()) {
            throw new DomainException('Test is already active.');
        }
        $this->status = Status::active();
    }
    public function isActive(): bool
    {
        return $this->status->isActive();
    }
}
