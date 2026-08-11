<?php

declare(strict_types=1);

namespace App\Course\Service\Downloader;

interface DirectoryCleanerInterface
{
    public function cleanDirectory(string $path): void;
}
