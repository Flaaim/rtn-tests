<?php

declare(strict_types=1);

namespace App\Course\Test\Unit\Service\Downloader;

use App\Course\Service\Downloader\DirectoryCleaner;
use App\SharedDomain\Filesystem\InMemoryFileSystemPath;
use DomainException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @internal
 * @coversNothing
 */
final class DirectoryCleanerTest extends WebTestCase
{
    private InMemoryFileSystemPath $filesystem;

    protected function setUp(): void
    {
        parent::setUp();
        $this->filesystem = InMemoryFileSystemPath::createReal();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->filesystem->clear();
    }

    public function testRemove(): void
    {
        $this->createFileInDirectory('one', 'content');

        self::assertDirectoryExists($this->filesystem->getValue() . '/one');

        $files = glob($this->filesystem->getValue() . '/one/*.*');

        self::assertNotFalse($files);
        self::assertCount(1, $files);
        $creator = new DirectoryCleaner(new Filesystem());

        $creator->cleanDirectory($this->filesystem->getValue() . '/one');

        self::assertDirectoryDoesNotExist($this->filesystem->getValue() . '/one');
    }

    private function createFileInDirectory(string $path, string $content): void
    {
        $dirPath = $this->filesystem->getValue() . \DIRECTORY_SEPARATOR . $path;

        $dirResult = mkdir($dirPath, 0o777, true);

        if (false === $dirResult) {
            throw new DomainException('Could not create directory');
        }

        $filePath = $dirPath . \DIRECTORY_SEPARATOR . uniqid('test_', true);

        $fileResult = file_put_contents($filePath, $content);

        if (false === $fileResult) {
            throw new DomainException('Unable to write file ' . $filePath);
        }
    }
}
