<?php

declare(strict_types=1);

namespace App\Course\Command\Course\ProtectQuestions;

use App\Course\Entity\Course\CourseId;
use App\Course\Entity\Course\CourseRepository;
use App\Infrastructure\Doctrine\Flusher;

final readonly class Handler
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private CourseRepository $courses,
        private Flusher $flusher,
    ) {}

    public function handle(Command $command): void
    {
        $course = $this->courses->get(new CourseId($command->courseId));

        $course->protectQuestions();

        $this->flusher->flush();
    }
}
