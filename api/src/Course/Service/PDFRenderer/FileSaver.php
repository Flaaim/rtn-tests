<?php

declare(strict_types=1);

namespace App\Course\Service\PDFRenderer;

use DomainException;
use Throwable;

final class FileSaver implements FileSaverInterface
{
    public function save(string $destinationFilePath, string $content): void
    {
        $fileHandler = fopen($destinationFilePath, 'wb');

        if (!$fileHandler) {
            throw new DomainException(\sprintf('Не удалось открыть файл "%s" для записи', $destinationFilePath));
        }
        try {
            fwrite($fileHandler, $content);
        } catch (Throwable $throwable) {
            if (\is_resource($fileHandler)) {
                fclose($fileHandler);
            }
            if (file_exists($destinationFilePath)) {
                unlink($destinationFilePath);
            }
            throw $throwable;
        } finally {
            if (\is_resource($fileHandler)) {
                fclose($fileHandler);
            }
        }
    }
}
