<?php

declare(strict_types=1);

namespace App\CourseAnswers\Entity\Payment;

use App\CourseAnswers\Event\Payment\PaymentConfirmed;
use App\SharedDomain\AggregateRoot;
use App\SharedDomain\Event\EventTrait;
use DateTimeImmutable;
use DomainException;

final class Payment implements AggregateRoot
{
    use EventTrait;
    private DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $confirmedAt = null;

    public function __construct(
        private PaymentId $id,
        private string $email,
        private string $courseId,
        private string $externalId,
        private PaymentStatus $status,
        private Amount $amount,
    ) {
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): PaymentId
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function getConfirmedAt(): ?DateTimeImmutable
    {
        return $this->confirmedAt;
    }

    public function getCourseId(): string
    {
        return $this->courseId;
    }

    public function getExternalId(): string
    {
        return $this->externalId;
    }

    public function getStatus(): PaymentStatus
    {
        return $this->status;
    }

    public function getAmount(): Amount
    {
        return $this->amount;
    }

    public function confirm(): void
    {
        if (PaymentStatus::SUCCEEDED === $this->status) {
            return;
        }

        if (PaymentStatus::PENDING !== $this->status) {
            throw new DomainException('Payment has already been confirmed.');
        }
        $this->status = PaymentStatus::SUCCEEDED;
        $this->confirmedAt = new DateTimeImmutable();

        $this->recordEvent(new PaymentConfirmed(
            $this->email,
            $this->courseId,
        ));
    }
}
