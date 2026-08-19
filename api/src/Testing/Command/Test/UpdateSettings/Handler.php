<?php

declare(strict_types=1);

namespace App\Testing\Command\Test\UpdateSettings;

use App\Infrastructure\Doctrine\Flusher;
use App\Testing\Entity\Test\Settings;
use App\Testing\Entity\Test\TestId;
use App\Testing\Entity\Test\TestRepository;

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

        $settings = new Settings(
            $command->numberOfTickets,
            $command->numberQuestionsInTicket,
            $command->allowedMistakes,
        );

        $allQuestionIds = $test->getSequentialQuestions();

        $test->changeSettings($settings, $allQuestionIds);

        $this->flusher->flush();
    }
}
