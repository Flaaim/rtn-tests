<?php

declare(strict_types=1);

namespace App\Course\Test\Unit\Service\FIleRenderer;

use App\Course\Service\Downloader\DirectoryCleanerInterface;
use App\Course\Service\Downloader\DirectoryCreatorInterface;
use App\Course\Service\FileRenderer\DirectoryPreparer;
use App\SharedDomain\Filesystem\FileSystemPathInterface;
use App\SharedDomain\Filesystem\InMemoryFileSystemPath;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class DirectoryPreparerTest extends TestCase
{
    /** @var DirectoryCreatorInterface&MockObject  */
    private DirectoryCreatorInterface $directoryCreator;
    /** @var DirectoryCleanerInterface&MockObject  */
    private DirectoryCleanerInterface $directoryCleaner;
    private FileSystemPathInterface $fileSystemPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileSystemPath = InMemoryFileSystemPath::createReal();
        $this->directoryCreator = $this->createMock(DirectoryCreatorInterface::class);
        $this->directoryCleaner = $this->createMock(DirectoryCleanerInterface::class);
    }

    public function testPrepare(): void
    {
        $preparer = new DirectoryPreparer(
            $this->fileSystemPath,
            $this->directoryCreator,
            $this->directoryCleaner
        );

        $this->directoryCleaner->expects(self::once())
            ->method('cleanDirectory')
            ->with(self::equalTo('/tmp/phpunit_real_storage/1d71cd68-e737-4eb4-8395-5e8650fa3657'));

        $this->directoryCreator->expects(self::once())
            ->method('createDirectory')
            ->with(self::equalTo('/tmp/phpunit_real_storage/1d71cd68-e737-4eb4-8395-5e8650fa3657'));

        $dir = $preparer->prepare('1d71cd68-e737-4eb4-8395-5e8650fa3657');

        self::assertEquals('/tmp/phpunit_real_storage/1d71cd68-e737-4eb4-8395-5e8650fa3657', $dir);
    }
}
