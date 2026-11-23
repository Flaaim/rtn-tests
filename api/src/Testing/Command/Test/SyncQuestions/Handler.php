<?php

declare(strict_types=1);

namespace App\Testing\Command\Test\SyncQuestions;

use App\Infrastructure\Doctrine\Flusher;
use App\Testing\Entity\Test\TestId;
use App\Testing\Entity\Test\TestRepository;
use App\Testing\Interfaces\CourseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(method: 'handle')]
final readonly class Handler
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private CourseInterface $course,
        private TestRepository $tests,
        private Flusher $flusher,
        private LoggerInterface $logger,
    ) {}

    public function handle(Command $command): void
    {
        $test = $this->tests->get(new TestId($command->id));
        $allQuestionIds = [];

        foreach ($test->getCourseId() as $courseId) {
            $courseQuestions = $this->course->getQuestionIds($courseId);
            $allQuestionIds = array_merge($allQuestionIds, $courseQuestions);
        }

        if (empty($allQuestionIds)) {
            if ($test->isActive()) {
                $test->deactivate();
                $this->flusher->flush();
            }

            $this->logger->error('Test deactivated: no questions found after course sync', [
                'testId' => $command->id,
            ]);
        }
        $allQuestionIds = array_unique($allQuestionIds);

        shuffle($allQuestionIds);

        $test->syncQuestions($allQuestionIds);

        $this->flusher->flush();
    }
}
