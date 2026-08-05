<?php

declare(strict_types=1);

namespace App\Testing\MessageHandler;

use App\Infrastructure\Doctrine\Flusher;
use App\Testing\Entity\CourseId;
use App\Testing\Entity\CourseRepository;
use App\Testing\Entity\CourseStatus;
use App\Testing\Event\CourseCreated;
use App\Testing\Service\CourseMediaDownloader;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/** @psalm-suppress UnusedClass */
#[AsMessageHandler]
final class CourseCreatedHandler
{
    public function __construct(
        private readonly CourseMediaDownloader $courseMediaDownloader,
        private readonly CourseRepository $courses,
        private readonly Flusher $flusher,
    ) {}

    public function __invoke(CourseCreated $event): void
    {
        $courseId = $event->id;

        $course = $this->courses->get(new CourseId($courseId));

        $this->courseMediaDownloader->downloadMedia($course->getQuestions(), $courseId);

        $course->updateStatus(CourseStatus::created());

        $this->flusher->flush();
    }
}
