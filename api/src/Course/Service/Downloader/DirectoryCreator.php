<?php

declare(strict_types=1);

namespace App\Course\Service\Downloader;

use DomainException;
use Symfony\Component\Filesystem\Filesystem;
use Throwable;

final class DirectoryCreator implements DirectoryCreatorInterface
{
    public function __construct(
        private readonly Filesystem $filesystem,
    ) {}

    public function createDirectory(string $path): void
    {
        try {
            if (!$this->filesystem->exists($path)) {
                $this->filesystem->mkdir($path);
            }
        } catch (Throwable $exception) {
            throw new DomainException("Unable to create directory {$path} " . $exception->getMessage());
        }
    }
}
