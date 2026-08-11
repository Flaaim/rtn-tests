<?php

declare(strict_types=1);

namespace App\Course\Service\Downloader;

interface FilenameGeneratorInterface
{
    public function generateFilename(string $url): string;
}
