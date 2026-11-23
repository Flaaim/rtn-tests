<?php

declare(strict_types=1);

namespace App\Event\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'processed_events')]
final class ProcessedEvent
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $messageId;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $processedAt;

    public function __construct(string $messageId)
    {
        $this->messageId = $messageId;
        $this->processedAt = new DateTimeImmutable();
    }

    public function getMessageId(): string
    {
        return $this->messageId;
    }

    public function getProcessedAt(): DateTimeImmutable
    {
        return $this->processedAt;
    }
}
