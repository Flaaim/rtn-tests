<?php

declare(strict_types=1);

namespace App\Testing\Service\Downloader;

interface FilenameGeneratorInterface
{
    public function generateFilename(string $url): string;
}
