<?php

declare(strict_types=1);

namespace App\Course\Command\Course\RenderToFile;

use App\Course\Query\Course\CourseFetcherInterface;
use App\Course\Service\FileRenderer\FileRendererInterface;
use DomainException;

final readonly class Handler
{
    public function __construct(
        private CourseFetcherInterface $courses,
        private FileRendererInterface $answersToPDFRenderer,
    ) {}

    public function handle(Command $command): void
    {
        $questions = $this->courses->getQuestionsByCourseIds([$command->courseId]);

        if (empty($questions)) {
            throw new DomainException('Courses questions to render not found.');
        }

        $this->answersToPDFRenderer->render($questions, $command->courseId);
    }
}
