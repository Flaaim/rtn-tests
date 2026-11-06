<?php

declare(strict_types=1);

namespace App\CourseAnswers\Command\Payment\Confirm;

final readonly class Command
{
    public function __construct(
        public string $externalId
    ) {}
}
