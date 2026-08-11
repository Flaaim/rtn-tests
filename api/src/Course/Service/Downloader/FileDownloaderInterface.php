<?php

declare(strict_types=1);

namespace App\Course\Service\Downloader;

interface FileDownloaderInterface
{
    public function download(string $url, string $destinationFilePath): void;
}
