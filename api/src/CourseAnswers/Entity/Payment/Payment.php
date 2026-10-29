<?php

declare(strict_types=1);

namespace App\CourseAnswers\Entity\Payment;

use DateTimeImmutable;

final class Payment
{
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

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

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
}
