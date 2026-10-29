<?php

declare(strict_types=1);

namespace App\Course\Service\PDFRenderer;

interface FileSaverInterface
{
    public function save(string $destinationFilePath, string $content): void;
}
