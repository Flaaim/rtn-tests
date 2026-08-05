<?php

declare(strict_types=1);

namespace App\Testing\Test\Unit\Service\Downloader;

use App\SharedDomain\Filesystem\InMemoryFileSystemPath;
use App\Testing\Service\Downloader\FileDownloader;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

/**
 * @internal
 * @coversNothing
 */
final class FileDownloaderTest extends TestCase
{
    private InMemoryFileSystemPath $fileSystem;

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
        $downloader = new FileDownloader($mockClient);
        $mockResponse = MockResponse::fromFile($this->createFile());
        $mockClient->setResponseFactory([$mockResponse]);

        mkdir($this->fileSystem->getValue() . \DIRECTORY_SEPARATOR . 'images', 0o777, true);

        $destinationFilePath = $this->fileSystem->getValue() . \DIRECTORY_SEPARATOR . 'images' . \DIRECTORY_SEPARATOR . 'file.jpg';

        $downloader->download(
            'https://olimpoks.hydroschool.ru/QuestionImages/c92099/9fef1bcf-9c6c-4010-a670-3dc105abc574/10/1.jpg',
            $destinationFilePath
        );

        self::assertFileExists($destinationFilePath);
    }

    private function createFile(string $extension = 'jpg'): string
    {
        $ext = strtolower($extension);

        $imageMap = [
            'jpg'  => '/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=',
            'jpeg' => '/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=',
            'png'  => 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=',
        ];
        if (!isset($imageMap[$ext])) {
            throw new RuntimeException(\sprintf('Unsupported image extension: "%s"', $extension));
        }

        $filePath = $this->fileSystem->getValue() . \DIRECTORY_SEPARATOR . uniqid('test_', true) . '.' . $ext;

        $binaryContent = base64_decode($imageMap[$ext], true);
        if (false === $binaryContent) {
            throw new RuntimeException(\sprintf('Binary content is not valid: "%s"', $imageMap[$ext]));
        }
        $result = file_put_contents($filePath, $binaryContent);

        if (false === $result) {
            throw new RuntimeException(\sprintf('Failed to write file to path: "%s"', $filePath));
        }

        return $filePath;
    }
}
