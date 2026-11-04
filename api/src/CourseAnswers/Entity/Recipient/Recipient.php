<?php

declare(strict_types=1);

namespace App\CourseAnswers\Entity\Recipient;

use App\CourseAnswers\Event\RecipientCreated;
use App\SharedDomain\AggregateRoot;
use App\SharedDomain\Event\EventTrait;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;

#[ORM\Entity]
#[ORM\Table(name: 'product_recipients')]
final class Recipient implements AggregateRoot
{
    use EventTrait;
    #[Column(type: 'string', enumType: Status::class)]
    private Status $status;

    public function __construct(
        #[ORM\Id]
        #[Column(type: 'recipient_id', unique: true)]
        private RecipientId $recipientId,
        #[Column(type: 'string', length: 255)]
        private string $productId,
        #[Column(type: 'recipient_email')]
        private Email $email,
        #[Column(type: 'datetime_immutable')]
        private DateTimeImmutable $createdAt,
        #[Column(type: 'datetime_immutable', nullable: true)]
        private ?DateTimeImmutable $sendAt = null,
        #[Column(type: 'string', nullable: true)]
        private ?string $failedReason = null,
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

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getSendAt(): ?DateTimeImmutable
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
        $this->sendAt = new DateTimeImmutable();
    }

    public function isFailed(): bool
    {
        return Status::FAILED === $this->status;
    }

    public function failed(string $reason): void
    {
        $this->status = Status::FAILED;
        $this->failedReason = $reason;
        $this->sendAt = null;
    }

    public function getFailedReason(): ?string
    {
        return $this->failedReason;
    }
}
