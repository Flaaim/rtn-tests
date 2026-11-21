<?php

declare(strict_types=1);

namespace App\Course\MessageHandler;

use App\Course\Entity\Course\CourseId;
use App\Course\Entity\Course\CourseRepository;
use App\Course\Entity\Course\Question;
use App\Course\Event\CourseProcessed;
use App\Course\Service\CourseMediaDownloader;
use App\Infrastructure\Doctrine\Flusher;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CourseProcessedHandler
{
    public function __construct(
        private CourseMediaDownloader $courseMediaDownloader,
        private CourseRepository $courses,
        private Flusher $flusher,
    ) {}

    public function __invoke(CourseProcessed $event): void
    {
        $courseId = $event->courseId;

        $course = $this->courses->get(new CourseId($courseId));

        $this->courseMediaDownloader->downloadMedia($course->getQuestions(), $courseId);

        /** @var Question $question */
        foreach ($course->getQuestions() as $question) {
            $question->markAnswersAsUpdated();
        }

        $course->markAsCreated();

        $this->flusher->flush();
    }
}
