<?php

declare(strict_types=1);

namespace App\Course\MessageHandler;

use App\Course\Entity\Course\CourseId;
use App\Course\Entity\Course\CourseRepository;
use App\Course\Entity\Course\Question;
use App\Course\Entity\Course\Status;
use App\Course\Event\CourseUpdated;
use App\Course\Service\CourseMediaDownloader;
use App\Infrastructure\Doctrine\Flusher;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/** @psalm-suppress UnusedClass */
#[AsMessageHandler]
final class CourseUpdatedHandler
{
    public function __construct(
        private readonly CourseMediaDownloader $courseMediaDownloader,
        private readonly CourseRepository $courses,
        private readonly Flusher $flusher,
    ) {}

    public function __invoke(CourseUpdated $event): void
    {
        $courseId = $event->id;

        $course = $this->courses->get(new CourseId($courseId));

        $this->courseMediaDownloader->downloadMedia($course->getQuestions(), $courseId);

        /** @var Question $question */
        foreach ($course->getQuestions() as $question) {
            $question->markAnswersAsUpdated();
        }

        $course->updateStatus(Status::created());

        $this->flusher->flush();
    }
}
