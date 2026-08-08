<?php

declare(strict_types=1);

namespace App\Testing\MessageHandler;

use App\Infrastructure\Doctrine\Flusher;
use App\Testing\Entity\Course\CourseRepository;
use App\Testing\Event\CourseUpdated;
use App\Testing\Service\CourseMediaDownloader;
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
    }
}
