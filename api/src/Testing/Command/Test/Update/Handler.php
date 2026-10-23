<?php

declare(strict_types=1);

namespace App\Testing\Command\Test\Update;

use App\Infrastructure\Doctrine\Flusher;
use App\Testing\Entity\Test\TestId;
use App\Testing\Entity\Test\TestRepository;
use App\Testing\Interfaces\CourseInterface;

final readonly class Handler
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private CourseInterface $course,
        private TestRepository $tests,
        private Flusher $flusher
    ) {}

    public function handle(Command $command): void
    {
        $courseIds = $command->courseIds;
        $test = $this->tests->get(new TestId($command->id));

        $allQuestionIds = [];

        foreach ($courseIds as $courseId) {
            $courseQuestions = $this->course->getQuestionIds($courseId);
            $allQuestionIds = array_merge($allQuestionIds, $courseQuestions);
        }

        $allQuestionIds = array_unique($allQuestionIds);

        shuffle($allQuestionIds);

        $test->updateTickets($courseIds, $allQuestionIds);

        $this->flusher->flush();
    }
}
