<?php

declare(strict_types=1);

namespace App\Course\Test\Unit\Service\PDFRenderer;

use App\Course\Service\PDFRenderer\FileSaver;
use App\SharedDomain\Filesystem\InMemoryFileSystemPath;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @internal
 * @coversNothing
 */
final class FileSaverTest extends TestCase
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

    public function testSave(): void
    {
        $saver = new FileSaver();
        mkdir($this->fileSystem->getValue() . \DIRECTORY_SEPARATOR . 'courses', 0o777, true);
        $destinationFilePath = $this->fileSystem->getValue() . \DIRECTORY_SEPARATOR . 'courses' . \DIRECTORY_SEPARATOR . 'pdf.jpg';

        $saver->save($destinationFilePath, $this->getBinaryContent());

        self::assertFileExists($destinationFilePath);
    }

    private function getBinaryContent(): string
    {
        $content = 'test';
        $binaryContent = base64_decode($content, strict: true);
        if (false === $binaryContent) {
            throw new RuntimeException('Binary content is not valid');
        }
        return $binaryContent;
    }
}
