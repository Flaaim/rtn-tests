<?php

declare(strict_types=1);

namespace App\Course\Test\Unit\Service\Downloader;

use App\Course\Service\Downloader\DirectoryCreator;
use App\SharedDomain\Filesystem\InMemoryFileSystemPath;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @internal
 * @coversNothing
 */
final class DirectoryCreatorTest extends TestCase
{
    private InMemoryFileSystemPath $fileSystem;

    protected function setUp(): void
    {
        $this->fileSystem = InMemoryFileSystemPath::createReal();
    }

    protected function tearDown(): void
    {
        $this->fileSystem->clear();
    }

    public function testSuccess(): void
    {
        $creator = new DirectoryCreator(new Filesystem());
        $creator->createDirectory($dir = $this->fileSystem->getValue() . '/one');

        self::assertDirectoryExists($dir);
    }
}
