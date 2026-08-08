<?php

declare(strict_types=1);

namespace App\Testing\Command\Course\Update;

use App\Infrastructure\Doctrine\Flusher;
use App\Testing\Entity\Course\CourseId;
use App\Testing\Entity\Course\CourseRepository;
use App\Testing\Service\QuestionExtractor;

final class Handler
{
    /** @psalm-suppress PossiblyUnusedMethod  */
    public function __construct(
        private readonly QuestionExtractor $questionExtractor,
        private readonly CourseRepository $courses,
        private readonly Flusher $flusher
    ) {}

    public function handle(Command $command): void
    {
        $course = $this->courses->get(new CourseId($command->id));

        $extracted = $this->questionExtractor->extract($command->draft);

        $course->addQuestions($extracted);

        $this->flusher->flush();
    }
}
