<?php

declare(strict_types=1);

namespace App\Testing\Command\Course\Add;

use App\Infrastructure\Doctrine\Flusher;
use App\Testing\Entity\Course\Course;
use App\Testing\Entity\Course\CourseId;
use App\Testing\Entity\Course\CourseRepository;
use App\Testing\Service\QuestionExtractor;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;

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
            new ArrayCollection($extracted),
            new DateTimeImmutable()
        );

        $this->courses->add($course);

        $this->flusher->flush();
    }
}
