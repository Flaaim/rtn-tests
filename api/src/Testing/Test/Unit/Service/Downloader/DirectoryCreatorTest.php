<?php

declare(strict_types=1);

namespace App\Testing\Test\Unit\Service\Downloader;

use App\SharedDomain\Filesystem\InMemoryFileSystemPath;
use App\Testing\Service\Downloader\DirectoryCreator;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class DirectoryCreatorTest extends TestCase
{
    private InMemoryFileSystemPath $fileSystem;

    protected function setUp(): void
    {
        $this->fileSystem = InMemoryFileSystemPath::create();
    }

    protected function tearDown(): void
    {
        $this->fileSystem->clear();
    }

    public function testSuccess(): void
    {
        $creator = new DirectoryCreator();
        $creator->createDirectory($dir = $this->fileSystem->getValue() . '/one');

        self::assertDirectoryExists($dir);
    }
}
