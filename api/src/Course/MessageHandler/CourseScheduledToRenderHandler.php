<?php

declare(strict_types=1);

namespace App\Course\MessageHandler;

use App\Course\Entity\Course\CourseId;
use App\Course\Entity\Course\CourseRepository;
use App\Course\Event\CourseScheduledToRender;
use App\Course\Service\FileRenderer\FileRendererInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CourseScheduledToRenderHandler
{
    public function __construct(
        private FileRendererInterface $answersToPDFRenderer,
        private CourseRepository $courses,
    ) {}

    public function __invoke(CourseScheduledToRender $event): void
    {
        $courseId = $event->courseId;

        $course = $this->courses->get(new CourseId($courseId));

        $this->answersToPDFRenderer->render($course->getQuestions(), $courseId);
    }
}
