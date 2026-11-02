<?php

declare(strict_types=1);

namespace App\Course\MessageHandler;

use App\Course\Entity\Course\CourseId;
use App\Course\Entity\Course\CourseRepository;
use App\Course\Event\CourseRendered;
use App\Course\Event\CourseScheduledToRender;
use App\Course\Service\FileRenderer\FileRendererFacade;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

/** @psalm-suppress UnusedClass */
#[AsMessageHandler]
final readonly class CourseScheduledToRenderHandler
{
    public function __construct(
        private FileRendererFacade $fileRendererFacade,
        private CourseRepository $courses,
        private MessageBusInterface $messageBus,
    ) {}

    public function __invoke(CourseScheduledToRender $event): void
    {
        $courseId = $event->courseId;

        $course = $this->courses->get(new CourseId($courseId));

        $file = $this->fileRendererFacade->renderAndSave($courseId, $course->getQuestions());

        $this->messageBus->dispatch(
            new CourseRendered($course->getCipher(), $file)
        );
    }
}
