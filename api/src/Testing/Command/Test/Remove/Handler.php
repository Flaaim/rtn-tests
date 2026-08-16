<?php

declare(strict_types=1);

namespace App\Testing\Command\Test\Remove;

use App\Infrastructure\Doctrine\Flusher;
use App\Testing\Entity\Test\TestId;
use App\Testing\Entity\Test\TestRepository;
use DomainException;

final class Handler
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private readonly TestRepository $tests,
        private readonly Flusher $flusher,
    ) {}

    public function handle(Command $command): void
    {
        $test = $this->tests->get(new TestId($command->id));

        if ($test->isActive()) {
            throw new DomainException('Can not remove active test.');
        }
        $this->tests->remove($test);

        $this->flusher->flush();
    }
}
