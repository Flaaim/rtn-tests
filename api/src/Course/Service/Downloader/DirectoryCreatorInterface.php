<?php

declare(strict_types=1);

namespace App\Course\Service\Downloader;

interface DirectoryCreatorInterface
{
    public function createDirectory(string $path): void;
}
