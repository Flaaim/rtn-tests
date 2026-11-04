<?php

declare(strict_types=1);

namespace App\CourseAnswers\Test\Builder;

use App\CourseAnswers\Entity\Recipient\Email;
use App\CourseAnswers\Entity\Recipient\Recipient;
use App\CourseAnswers\Entity\Recipient\RecipientId;
use App\CourseAnswers\Entity\Recipient\Status;
use DateTimeImmutable;

final class RecipientBuilder
{
    private RecipientId $recipientId;
    private string $productId;
    private Email $email;
    private Status $status;
    private bool $isSent = false;
    private DateTimeImmutable $sentAt;

    public function __construct()
    {
        $this->recipientId = new RecipientId('9a28a972-3ce3-4b43-9ed6-964df29c134c');
        $this->productId = '5dc715b0-f5f1-43e0-9492-71a6adbb09a4';
        $this->email = new Email('test@mail.ru');
        $this->status = Status::PENDING;
        $this->sentAt = new DateTimeImmutable();
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function withRecipientId(RecipientId $recipientId): self
    {
        $clone = clone $this;
        $clone->recipientId = $recipientId;
        return $clone;
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function withProductId(string $productId): self
    {
        $clone = clone $this;
        $clone->productId = $productId;
        return $clone;
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function withEmail(Email $email): self
    {
        $clone = clone $this;
        $clone->email = $email;
        return $clone;
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function sent(): self
    {
        $clone = clone $this;
        $clone->isSent = true;
        return $clone;
    }

    public function build(): Recipient
    {
        $recipient = new Recipient(
            $this->recipientId,
            $this->productId,
            $this->email,
            $this->sentAt
        );

        if ($this->isSent) {
            $recipient->sent();
        }

        return $recipient;
    }
}
