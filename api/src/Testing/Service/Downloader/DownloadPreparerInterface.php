<?php

declare(strict_types=1);

namespace App\Testing\Service\Downloader;

interface DownloadPreparerInterface
{
    public function prepareFile(string $url, string $relativePathDir): string;
}
