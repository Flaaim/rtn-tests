<?php

declare(strict_types=1);

namespace App\Event\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

#[ORM\Entity]
#[ORM\Table(name: 'event_store_events')]
final class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;
    #[ORM\Column(type: 'string', length: 255)]
    private string $aggregateId;
    #[ORM\Column(type: 'string', length: 255)]
    private string $type;
    #[ORM\Column(type: 'text')]
    private string $payload;
    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    public function __construct(string $aggregateId, string $type, string $payload)
    {
        Assert::notEmpty($aggregateId);
        Assert::notEmpty($type);

        $this->aggregateId = $aggregateId;
        $this->type = $type;
        $this->payload = $payload;
        $this->createdAt = new DateTimeImmutable();
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function getId(): int
    {
        return $this->id ?? 0;
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function getType(): string
    {
        return $this->type;
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function getPayload(): string
    {
        return $this->payload;
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
    /** @psalm-suppress PossiblyUnusedMethod */
    public function getAggregateId(): string
    {
        return $this->aggregateId;
    }
}
