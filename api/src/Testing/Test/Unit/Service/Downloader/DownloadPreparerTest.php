<?php

declare(strict_types=1);

namespace App\Testing\Test\Unit\Service\Downloader;

use App\SharedDomain\Filesystem\FileSystemPathInterface;
use App\SharedDomain\Filesystem\InMemoryFileSystemPath;
use App\Testing\Service\Downloader\DirectoryCreatorInterface;
use App\Testing\Service\Downloader\DownloadPreparer;
use App\Testing\Service\Downloader\FilenameGeneratorInterface;
use DomainException;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class DownloadPreparerTest extends TestCase
{
    private FileSystemPathInterface $fileSystem;
    private FilenameGeneratorInterface $fileNameGenerator;
    private DirectoryCreatorInterface $directoryCreator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileSystem = InMemoryFileSystemPath::createReal();
    }

    public function testSuccess(): void
    {
        $relativePathDir = '840e0656-be5c-4459-a2f7-9cb09cdef848';
        $preparer = new DownloadPreparer(
            $this->fileSystem,
            $fileNameGenerator = $this->createMock(FilenameGeneratorInterface::class),
            $directoryCreator = $this->createMock(DirectoryCreatorInterface::class)
        );

        $fileNameGenerator->expects(self::once())->method('generateFilename')->willReturn('filename.jpg');
        $directoryCreator->expects(self::once())->method('createDirectory');
        $newUrl = $preparer->prepareFile(
            'https://olimpoks.hydroschool.ru/QuestionImages/c92099/9fef1bcf-9c6c-4010-a670-3dc105abc574/10/1.jpg',
            $relativePathDir
        );

        self::assertEquals('/tmp/phpunit_real_storage/840e0656-be5c-4459-a2f7-9cb09cdef848/filename.jpg', $newUrl);
    }

    public function testEmpty(): void
    {
        $preparer = new DownloadPreparer(
            $this->fileSystem,
            $this->createStub(FilenameGeneratorInterface::class),
            $this->createStub(DirectoryCreatorInterface::class),
        );
        self::expectException(DomainException::class);
        self::expectExceptionMessage('Url or relativePathDir can not be empty.');
        $preparer->prepareFile('', '');
    }
}
