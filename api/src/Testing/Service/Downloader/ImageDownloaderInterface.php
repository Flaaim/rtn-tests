<?php

declare(strict_types=1);

namespace App\Testing\Service\Downloader;

interface ImageDownloaderInterface
{
    public function download(string $url, string $filePath): void;
}
