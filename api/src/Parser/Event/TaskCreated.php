<?php

declare(strict_types=1);

namespace App\Parser\Event;

final class TaskCreated
{
    public function __construct(
        public string $id,
    ) {}
}
