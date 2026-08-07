<?php

declare(strict_types=1);

namespace App\Testing\Command\Course\Rename;

use App\Infrastructure\Doctrine\Flusher;
use App\Testing\Entity\Course\CourseId;
use App\Testing\Entity\Course\CourseRepository;

final class Handler
{
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
