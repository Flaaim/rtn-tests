<?php

declare(strict_types=1);

namespace App\Testing\Service\Downloader;

use App\SharedDomain\Filesystem\FileSystemPathInterface;
use DomainException;

final class DownloadPreparer implements DownloadPreparerInterface
{
    public function __construct(
        private readonly FileSystemPathInterface $fileSystemPath,
        private readonly FilenameGeneratorInterface $filenameGenerator,
        private readonly DirectoryCreatorInterface $directoryCreator
    ) {}

    public function prepareFile(string $url, string $relativePathDir): string
    {
        if ('' === $url || '' === $relativePathDir) {
            throw new DomainException('Url or relativePathDir can not be empty.');
        }
        $path = $this->fileSystemPath->getValue() . \DIRECTORY_SEPARATOR . $relativePathDir;

        $filename = $this->filenameGenerator->generateFilename($url);

        $this->directoryCreator->createDirectory($path);

        return $path . \DIRECTORY_SEPARATOR . $filename;
    }
}
