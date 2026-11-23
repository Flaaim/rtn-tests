<?php

declare(strict_types=1);

namespace App\Testing\MessageHandler\Course;

use App\Course\Event\CourseQuestionsUpdated;
use App\Testing\Command\Test\SyncQuestions\Command;
use App\Testing\Query\Test\GetAllTestIdByCourseId\Query;
use App\Testing\Query\Test\GetAllTestIdByCourseId\QueryHandler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

/** @psalm-suppress UnusedClass */
#[AsMessageHandler]
final readonly class CourseQuestionsUpdatedHandler
{
    public function __construct(
        private QueryHandler $queryHandler,
        private MessageBusInterface $messageBus,
    ) {}

    public function __invoke(CourseQuestionsUpdated $event): void
    {
        $courseId = $event->courseId;

        $tests = $this->queryHandler->handle(new Query($courseId));

        foreach ($tests as $test) {
            $this->messageBus->dispatch(new Command($test->testId));
        }
    }
}
