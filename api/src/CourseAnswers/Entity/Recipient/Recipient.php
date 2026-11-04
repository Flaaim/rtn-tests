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

    public function __construct(
        private RecipientId $recipientId,
        private string $productId,
        private Email $email,
        private Status $status,
        private DateTimeImmutable $sendAt,
    ) {
        $this->recordEvent(new RecipientCreated(
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
}
