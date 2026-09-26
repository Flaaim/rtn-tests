<?php

declare(strict_types=1);

namespace App\Testing\Command\Test\ChangeCategory;

use App\Infrastructure\Doctrine\Flusher;
use App\Testing\Entity\Test\TestId;
use App\Testing\Entity\Test\TestRepository;

final readonly class Handler
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private TestRepository $tests,
        private Flusher $flusher
    ) {}

    public function handle(Command $command): void
    {
        $test = $this->tests->get(new TestId($command->testId));

        $test->changeCategory($command->categoryId);

        $this->flusher->flush();
    }
}
