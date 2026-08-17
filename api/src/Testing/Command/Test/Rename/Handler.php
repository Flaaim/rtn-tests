<?php

declare(strict_types=1);

namespace App\Testing\Command\Test\Rename;

use App\Infrastructure\Doctrine\Flusher;
use App\Testing\Entity\Test\TestId;
use App\Testing\Entity\Test\TestRepository;

final class Handler
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private readonly TestRepository $tests,
        private readonly Flusher $flusher
    ) {}

    public function handle(Command $command): void
    {
        $currentTest = $this->tests->get(new TestId($command->id));

        $currentTest->rename(
            $command->name,
            $command->description,
        );

        $this->flusher->flush();
    }
}
