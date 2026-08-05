<?php

declare(strict_types=1);

namespace App\Testing\Service\Downloader;

use InvalidArgumentException;
use Ramsey\Uuid\Uuid;

final class FilenameGenerator implements FilenameGeneratorInterface
{
    public function generateFilename(string $url): string
    {
        if ('' === $url) {
            throw new InvalidArgumentException('Url cannot be empty');
        }
        $extension = pathinfo($url, PATHINFO_EXTENSION);

        $extension = $extension ?: 'jpg';

        return Uuid::uuid4()->toString() . '.' . $extension;
    }
}
