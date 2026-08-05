<?php

declare(strict_types=1);

namespace App\Testing\Service\Downloader;

use DomainException;

final class DirectoryCreator implements DirectoryCreatorInterface
{
    public function createDirectory(string $path): void
    {
        if (is_dir($path)) {
            return;
        }
        $status = mkdir($path, 0o777, true);
        if (false === $status) {
            throw new DomainException('Unable to create directory ' . $path);
        }
    }
}
