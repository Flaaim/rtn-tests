<?php

declare(strict_types=1);

namespace App\Course\MessageHandler;

use App\Course\Entity\Course\CourseId;
use App\Course\Entity\Course\CourseRepository;
use App\Course\Event\CourseScheduledToRender;
use App\Course\Service\FileRenderer\FileRendererFacade;
use App\Infrastructure\Doctrine\Flusher;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CourseScheduledToRenderHandler
{
    public function __construct(
        private FileRendererFacade $fileRendererFacade,
        private CourseRepository $courses,
        private Flusher $flusher,
    ) {}

    public function __invoke(CourseScheduledToRender $event): void
    {
        $courseId = $event->courseId;

        $course = $this->courses->get(new CourseId($courseId));

        $file = $this->fileRendererFacade->renderAndSave($courseId, $course->getQuestions());

        $course->renderFile($file);

        $this->flusher->flush();
    }
}
