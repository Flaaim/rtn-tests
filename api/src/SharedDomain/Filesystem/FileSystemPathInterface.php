<?php

declare(strict_types=1);

namespace App\SharedDomain\Filesystem;

interface FileSystemPathInterface
{
    public function getValue(): string;
}
