<?php

declare(strict_types=1);

namespace App\Course\MessageHandler;

use App\Course\Entity\Course\CourseId;
use App\Course\Entity\Course\CourseRepository;
use App\Course\Entity\Course\Question;
use App\Course\Entity\Course\Status;
use App\Course\Event\CourseCreated;
use App\Course\Event\CourseScheduledToRender;
use App\Course\Service\CourseMediaDownloader;
use App\Infrastructure\Doctrine\Flusher;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

/** @psalm-suppress UnusedClass */
#[AsMessageHandler]
final readonly class CourseCreatedHandler
{
    public function __construct(
        private CourseMediaDownloader $courseMediaDownloader,
        private CourseRepository $courses,
        private MessageBusInterface $messageBus,
        private Flusher $flusher,
    ) {}

    public function __invoke(CourseCreated $event): void
    {
        $courseId = $event->id;

        $course = $this->courses->get(new CourseId($courseId));

        $this->courseMediaDownloader->downloadMedia($course->getQuestions(), $courseId);

        /** @var Question $question */
        foreach ($course->getQuestions() as $question) {
            $question->markAnswersAsUpdated();
        }

        $this->messageBus->dispatch(new CourseScheduledToRender($courseId));

        $course->updateStatus(Status::created());

        $this->flusher->flush();
    }
}
