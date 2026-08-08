<?php

declare(strict_types=1);

namespace App\Testing\Service\Downloader;

use DomainException;
use Symfony\Component\Filesystem\Filesystem;
use Throwable;

final class DirectoryCleaner implements DirectoryCleanerInterface
{
    public function __construct(
        private readonly Filesystem $filesystem,
    ) {}

    public function cleanDirectory(string $path): void
    {
        try {
            if ($this->filesystem->exists($path)) {
                $this->filesystem->remove($path);
            }
        } catch (Throwable $throwable) {
            throw new DomainException("Unable to clean directory: {$path} " . $throwable->getMessage());
        }
    }
}
