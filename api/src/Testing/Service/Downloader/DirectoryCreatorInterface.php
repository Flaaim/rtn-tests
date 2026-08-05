<?php

declare(strict_types=1);

namespace App\Testing\Service\Downloader;

interface DirectoryCreatorInterface
{
    public function createDirectory(string $path): void;
}
