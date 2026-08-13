<?php

declare(strict_types=1);

namespace App\Testing\Event;

final class TestActivated
{
    public function __construct(
        public string $id,
    ) {}
}
