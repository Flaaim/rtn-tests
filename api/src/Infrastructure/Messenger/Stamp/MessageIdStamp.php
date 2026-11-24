<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger\Stamp;

use Symfony\Component\Messenger\Stamp\StampInterface;

final readonly class MessageIdStamp implements StampInterface
{
    public function __construct(
        public string $id,
    ) {}
}
