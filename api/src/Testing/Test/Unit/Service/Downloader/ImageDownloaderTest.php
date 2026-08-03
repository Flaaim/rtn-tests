<?php

declare(strict_types=1);

namespace App\Testing\Test\Unit\Service\Downloader;

use App\SharedDomain\Filesystem\FileSystemPathInterface;
use App\SharedDomain\Filesystem\InMemoryFileSystemPath;
use App\Testing\Service\Downloader\ImageDownloader;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

/**
 * @internal
 * @coversNothing
 */
final class ImageDownloaderTest extends TestCase
{
    private FileSystemPathInterface $fileSystem;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileSystem = InMemoryFileSystemPath::createReal();
    }

    protected function tearDown(): void
    {
        $this->fileSystem->clear();
    }

    public function testSuccess(): void
    {
        $mockClient = new MockHttpClient();
        $downloader = new ImageDownloader($mockClient);
        $newFilePath = $this->fileSystem->getValue() . \DIRECTORY_SEPARATOR . 'new-test.jpg';

        $mockResponse = MockResponse::fromFile($this->createFile());
        $mockClient->setResponseFactory([$mockResponse]);

        $downloader->download(
            'https://olimpoks.hydroschool.ru/QuestionImages/c92099/9fef1bcf-9c6c-4010-a670-3dc105abc574/10/1.jpg',
            $newFilePath
        );

        self::assertFileExists($this->fileSystem->getValue() . \DIRECTORY_SEPARATOR . 'new-test.jpg');
        self::assertEquals('test', file_get_contents($newFilePath));
    }

    private function createFile(): string
    {
        $filePath = $this->fileSystem->getValue() . \DIRECTORY_SEPARATOR . 'test.jpg';
        $result = file_put_contents($filePath, 'test');

        if (false === $result) {
            throw new RuntimeException('Failed to write file');
        }

        return $filePath;
    }
}
