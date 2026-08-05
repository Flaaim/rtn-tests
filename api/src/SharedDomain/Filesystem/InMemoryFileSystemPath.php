<?php

declare(strict_types=1);

namespace App\SharedDomain\Filesystem;

use FilesystemIterator;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class InMemoryFileSystemPath implements FileSystemPathInterface
{
    private string $value;
    /** @psalm-suppress UnusedProperty */
    private vfsStreamDirectory $root;
    private bool $isVfs;

    private function __construct(bool $useVfs = true)
    {
        $this->isVfs = $useVfs;

        if ($useVfs) {
            $this->root = vfsStream::setup('storage');
            $this->value = vfsStream::url('storage');
        } else {
            $this->value = sys_get_temp_dir() . '/phpunit_real_storage';
            if (!is_dir($this->value)) {
                mkdir($this->value, 0o777, true);
            }
        }
    }

    public static function create(): self
    {
        return new self();
    }

    public static function createReal(): self
    {
        return new self(false);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function clear(): void
    {
        if ($this->isVfs) {
            vfsStream::setup('storage');
        } else {
            if (is_dir($this->value)) {
                $files = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($this->value, FilesystemIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::CHILD_FIRST
                );

                foreach ($files as $fileinfo) {
                    $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
                    $todo($fileinfo->getRealPath());
                }
            }
        }
    }
}
