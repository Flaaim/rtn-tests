<?php

declare(strict_types=1);

namespace App\SharedDomain\Filesystem;

use Webmozart\Assert\Assert;

final class FileSystemPath implements FileSystemPathInterface
{
    private readonly string $path;

    public function __construct(string $path)
    {
        Assert::notEmpty($path);
        $this->path = rtrim($path, '/');
    }

    public function getValue(): string
    {
        return $this->path;
    }
}
