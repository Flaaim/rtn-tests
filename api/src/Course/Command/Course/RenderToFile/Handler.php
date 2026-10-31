<?php

declare(strict_types=1);

namespace App\Course\Command\Course\RenderToFile;

use App\Course\Event\CourseScheduledToRender;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class Handler
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private MessageBusInterface $messageBus,
    ) {}

    public function handle(Command $command): void
    {
        $this->messageBus->dispatch(
            new CourseScheduledToRender($command->courseId)
        );
    }
}
