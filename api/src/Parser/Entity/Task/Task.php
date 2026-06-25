<?php

declare(strict_types=1);

namespace App\Parser\Entity\Task;

use App\Parser\Entity\Parser\ParserId;
use App\Parser\Event\ParseEnded;
use App\Parser\Event\ParseFailed;
use App\Parser\Event\ParseLaunched;
use App\SharedDomain\AggregateRoot;
use App\SharedDomain\Event\EventTrait;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;

#[ORM\Entity]
#[ORM\Table(name: 'tasks')]
final class Task implements AggregateRoot
{
    use EventTrait;

    private Status $status;
    public function __construct(
        private TaskId $taskId,
        private ParserId $parserId,
        #[Column(type: 'string', length: 255)]
        private string $branchId,
        #[Column(type: 'string', length: 255)]
        private string $ticketId,
        private \DateTimeImmutable $createdAt,
        private ?string $draft = null,
        private ?string $failedReason = null,
    ) {

        $this->status = Status::processing();

        $this->recordEvent(new ParseLaunched(
            $this->taskId->getValue(),
            $this->parserId->getValue(),
            $this->branchId,
            $this->ticketId,
        ));
    }

    public function getId(): TaskId
    {
        return $this->taskId;
    }
    public function getParserId(): ParserId
    {
        return $this->parserId;
    }
    public function getBranchId(): string
    {
        return $this->branchId;
    }
    public function getTicketId(): string
    {
        return $this->ticketId;
    }
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
    public function getStatus(): Status
    {
        return $this->status;
    }
    public function getDraft(): ?string
    {
        return $this->draft;
    }
    public function getFailedReason(): ?string
    {
        return $this->failedReason;
    }
    public function ended(string $result): void
    {
        if($this->status->isEqual(Status::completed())) {
            throw new \DomainException('Task is already ended.');
        }
        $this->status = Status::completed();
        $this->draft = $result;
        $this->failedReason = null;

        $this->recordEvent(new ParseEnded(
            $this->taskId->getValue(),
            $this->parserId->getValue(),
        ));
    }
    public function failed(string $reason): void
    {
        if($this->status->isEqual(Status::failed())) {
            throw new \DomainException('Task already failed.');
        }

        $this->status = Status::failed();
        $this->failedReason = $reason;
        $this->draft = null;

        $this->recordEvent(new ParseFailed(
            $this->parserId->getValue(),
            $this->failedReason
        ));
    }
}
