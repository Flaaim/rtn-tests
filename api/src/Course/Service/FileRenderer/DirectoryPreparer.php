<?php

declare(strict_types=1);

namespace App\Course\Service\FileRenderer;

use App\Course\Service\Downloader\DirectoryCleanerInterface;
use App\Course\Service\Downloader\DirectoryCreatorInterface;
use App\SharedDomain\Filesystem\FileSystemPathInterface;

final readonly class DirectoryPreparer implements DirectoryPreparerInterface
{
    public function __construct(
        private FileSystemPathInterface $coursePath,
        private DirectoryCreatorInterface $directoryCreator,
        private DirectoryCleanerInterface $directoryCleaner,
    ) {}

    public function prepare(string $courseId): string
    {
        $dir = $this->coursePath->getValue() . \DIRECTORY_SEPARATOR . $courseId;

        $this->directoryCleaner->cleanDirectory($dir);
        $this->directoryCreator->createDirectory($dir);

        return $dir;
    }
}
