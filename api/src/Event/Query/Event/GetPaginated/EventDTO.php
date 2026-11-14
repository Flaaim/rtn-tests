<?php

declare(strict_types=1);

namespace App\Event\Query\Event\GetPaginated;

final readonly class EventDTO
{
    public function __construct(
        public int $eventId,
        public string $type,
        public string $payload,
        public string $createdAt,
        public string $aggregateId,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            eventId: $data['id'],
            type: $data['type'],
            payload: $data['payload'],
            createdAt: $data['created_at'],
            aggregateId: $data['aggregate_id'],
        );
    }
}
