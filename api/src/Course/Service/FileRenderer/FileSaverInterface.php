<?php

declare(strict_types=1);

namespace App\Course\Service\FileRenderer;

interface FileSaverInterface
{
    public function save(string $destinationFilePath, string $content): void;
}
