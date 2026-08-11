<?php

declare(strict_types=1);

namespace App\Course\Command\Course\Rename;

use App\Course\Entity\Course\CourseId;
use App\Course\Entity\Course\CourseRepository;
use App\Infrastructure\Doctrine\Flusher;

final class Handler
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private readonly CourseRepository $courses,
        private readonly Flusher $flusher
    ) {}

    public function handle(Command $command): void
    {
        $course = $this->courses->get(new CourseId($command->id));

        $course->rename($command->name, $command->cipher);

        $this->flusher->flush();
    }
}
