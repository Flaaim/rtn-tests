<?php

declare(strict_types=1);

namespace App\CourseAnswers\Command\Payment\Fail;

final readonly class Command
{
    public function __construct(
        public string $externalId
    ) {}
}
