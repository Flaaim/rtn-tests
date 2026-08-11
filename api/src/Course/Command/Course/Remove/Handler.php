<?php

declare(strict_types=1);

namespace App\Course\Command\Course\Remove;

use App\Course\Entity\Course\CourseId;
use App\Course\Entity\Course\CourseRepository;
use App\Course\Service\Downloader\DirectoryCleanerInterface;
use App\Infrastructure\Doctrine\Flusher;
use App\SharedDomain\Filesystem\FileSystemPathInterface;

final class Handler
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private readonly CourseRepository $courses,
        private readonly DirectoryCleanerInterface $cleaner,
        private readonly FileSystemPathInterface $fileSystemPath,
        private readonly Flusher $flusher
    ) {}

    public function handle(Command $command): void
    {
        $course = $this->courses->get(new CourseId($command->id));

        $questionDir = $this->fileSystemPath->getValue() . \DIRECTORY_SEPARATOR . $course->getCourseId()->getValue();

        $this->cleaner->cleanDirectory($questionDir);

        $this->courses->remove($course);

        $this->flusher->flush();
    }
}
