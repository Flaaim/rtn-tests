<?php

declare(strict_types=1);

namespace App\SharedDomain\Filesystem;

use Symfony\Component\Filesystem\Filesystem;

final class InMemoryFileSystemPath implements FileSystemPathInterface
{
    private string $value;
    private Filesystem $filesystem;

    private function __construct()
    {
        $this->filesystem = new Filesystem();
        $this->value = sys_get_temp_dir() . '/phpunit_real_storage';
        if (!$this->filesystem->exists($this->value)) {
            $this->filesystem->mkdir($this->value);
        }
    }

    public static function createReal(): self
    {
        return new self();
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function clear(): void
    {
        if ($this->filesystem->exists($this->value)) {
            $this->filesystem->remove($this->value);
        }
    }
}
