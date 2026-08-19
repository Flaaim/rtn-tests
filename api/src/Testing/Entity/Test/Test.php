<?php

declare(strict_types=1);

namespace App\Testing\Entity\Test;

use App\SharedDomain\AggregateRoot;
use App\SharedDomain\Event\EventTrait;
use App\Testing\Entity\Test\DTO\TicketDTO;
use App\Testing\Event\TestActivated;
use App\Testing\Event\TestCreated;
use App\Testing\Event\TestDeactivated;
use App\Testing\Event\TestRemoved;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use DomainException;
use Webmozart\Assert\Assert;

#[ORM\Entity]
#[ORM\Table(name: 'tests')]
final class Test implements AggregateRoot
{
    use EventTrait;
    #[ORM\Column(type: 'test_status')]
    private Status $status;
    /** @param array<int, array|TicketDTO> $tickets */
    #[ORM\Column(type: Types::JSON, options: ['jsonb' => true])]
    private array $tickets;

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
        #[ORM\Column(type: Types::JSON, options: ['jsonb' => true])]
        private array $courseIds,
        $questionIds,
        #[ORM\Column(type: 'string', length: 255, unique: true)]
        private string $slug,
        #[ORM\Column(type: 'datetime_immutable')]
        private DateTimeImmutable $createdAt,
        #[ORM\Embedded(class: Settings::class, columnPrefix: false)]
        private Settings $settings
    ) {
        Assert::notEmpty($questionIds, 'Question IDs should not be empty.');

        $this->regenerateTickets($questionIds);

        $this->status = Status::inactive();
        $this->recordEvent(new TestCreated($id->getValue()));
    }

    public function getId(): TestId
    {
        return $this->id;
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function getStatus(): Status
    {
        return $this->status;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function getCourseId(): array
    {
        return $this->courseIds;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return TicketDTO[]
     */
    public function getTickets(): array
    {
        if (isset($this->tickets[0]) && \is_array($this->tickets[0])) {
            $this->tickets = array_map(
                static fn (array $ticketData) => TicketDTO::fromArray($ticketData),
                $this->tickets
            );
        }
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
        foreach ($this->getTickets() as $ticket) {
            $allQuestions = array_merge($allQuestions, $ticket->questionIds);
        }

        return array_values(array_unique($allQuestions));
    }

    public function getSettings(): Settings
    {
        return $this->settings;
    }

    public function changeSettings(Settings $settings, array $allQuestionIds): void
    {
        if ($this->isActive()) {
            throw new DomainException('Cannot change settings of an active test.');
        }
        $this->settings = $settings;

        $this->regenerateTickets($allQuestionIds);
    }

    public function activate(): void
    {
        if ($this->isActive()) {
            throw new DomainException('Test is already active.');
        }
        $this->status = Status::active();

        $this->recordEvent(new TestActivated($this->id->getValue()));
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    public function deactivate(): void
    {
        if ($this->isInactive()) {
            throw new DomainException('Test is already inactive.');
        }

        $this->status = Status::inactive();

        $this->recordEvent(new TestDeactivated($this->id->getValue()));
    }

    public function isInactive(): bool
    {
        return $this->status->isInactive();
    }

    public function remove(): void
    {
        if ($this->isActive()) {
            throw new DomainException('Can not remove active test.');
        }

        $this->recordEvent(new TestRemoved($this->id->getValue()));
    }

    public function rename(
        ?string $name = null,
        ?string $description = null
    ): void {
        if ($this->isActive()) {
            throw new DomainException('Can not rename active test.');
        }

        if (null !== $name) {
            $this->name = $name;
        }
        if (null !== $description) {
            $this->description = $description;
        }
    }

    public function changeCipher(string $cipher, string $slug): void
    {
        if ($this->isActive()) {
            throw new DomainException('Can not change cipher of active test.');
        }
        $this->cipher = $cipher;
        $this->slug = $slug;
    }

    public function updateTickets(array $courseIds, array $allQuestionIds): void
    {
        if ($this->isActive()) {
            throw new DomainException('Can not update tickets of active test.');
        }
        $this->courseIds = $courseIds;

        $this->regenerateTickets($allQuestionIds);
    }

    private function regenerateTickets(array $allQuestionIds): void
    {
        /** @var TicketDTO[] $tickets */
        $this->tickets = [];

        if (empty($allQuestionIds)) {
            return;
        }
        $chunks = array_chunk($allQuestionIds, $this->settings->getNumberQuestionsInTicket());
        foreach ($chunks as $index => $chunk) {
            if ($index >= $this->settings->getNumberOfTickets()) {
                break;
            }

            $this->tickets[] = new TicketDTO(
                $index + 1,
                $chunk
            );
        }
    }
}
