<?php

declare(strict_types=1);

namespace App\Testing\Event;

final class TestChanged
{
    public function __construct(
        public string $id,
        public string $message,
    ) {}
}
