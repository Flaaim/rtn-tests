<?php

declare(strict_types=1);

namespace App\Course\Command\Course\Add;

use App\Course\Entity\Course\Course;
use App\Course\Entity\Course\CourseId;
use App\Course\Entity\Course\CourseRepository;
use App\Course\Service\QuestionExtractor;
use App\Infrastructure\Doctrine\Flusher;
use DateTimeImmutable;

final class Handler
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private readonly QuestionExtractor $questionExtractor,
        private readonly CourseRepository $courses,
        private readonly Flusher $flusher
    ) {}

    public function handle(Command $command): void
    {
        $courseId = CourseId::generate();

        $extracted = $this->questionExtractor->extract($command->draft);

        $course = new Course(
            $courseId,
            $command->name,
            $extracted,
            new DateTimeImmutable(),
            $command->cipher
        );

        $this->courses->add($course);

        $this->flusher->flush();
    }
}
