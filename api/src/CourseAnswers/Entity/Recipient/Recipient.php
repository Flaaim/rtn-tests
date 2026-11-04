<?php

declare(strict_types=1);

namespace App\CourseAnswers\Entity\Recipient;

use App\CourseAnswers\Event\RecipientCreated;
use App\SharedDomain\AggregateRoot;
use App\SharedDomain\Event\EventTrait;
use DateTimeImmutable;

final class Recipient implements AggregateRoot
{
    use EventTrait;

    private Status $status;

    public function __construct(
        private RecipientId $recipientId,
        private string $productId,
        private Email $email,
        private DateTimeImmutable $sendAt,
    ) {
        $this->status = Status::PENDING;

        $this->recordEvent(new RecipientCreated(
            $this->recipientId->getValue(),
            $this->productId,
            $this->email->getValue()
        ));
    }

    public function getRecipientId(): RecipientId
    {
        return $this->recipientId;
    }

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function getSendAt(): DateTimeImmutable
    {
        return $this->sendAt;
    }

    public function isSent(): bool
    {
        return Status::SENT === $this->status;
    }

    public function sent(): void
    {
        $this->status = Status::SENT;
    }
}
