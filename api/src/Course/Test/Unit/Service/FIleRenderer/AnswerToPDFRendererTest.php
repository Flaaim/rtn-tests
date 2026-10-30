<?php

declare(strict_types=1);

namespace App\Course\Test\Unit\Service\FIleRenderer;

use App\Course\Service\Downloader\DirectoryCleanerInterface;
use App\Course\Service\Downloader\DirectoryCreatorInterface;
use App\Course\Service\FileRenderer\AnswersToPDFRenderer;
use App\Course\Service\FileRenderer\FileSaverInterface;
use App\SharedDomain\Filesystem\FileSystemPathInterface;
use App\SharedDomain\Filesystem\InMemoryFileSystemPath;
use Nucleos\DompdfBundle\Wrapper\DompdfWrapperInterface;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

/**
 * @internal
 * @coversNothing
 */
final class AnswerToPDFRendererTest extends TestCase
{
    private FileSystemPathInterface $fileSystemPath;
    private DompdfWrapperInterface $wrapper;
    private FileSaverInterface $fileSaver;
    private DirectoryCreatorInterface $directoryCreator;
    private DirectoryCleanerInterface $directoryCleaner;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileSystemPath = InMemoryFileSystemPath::createReal();
        $this->wrapper = $this->createMock(DompdfWrapperInterface::class);
        $this->fileSaver = $this->createMock(FileSaverInterface::class);
        $this->directoryCreator = $this->createMock(DirectoryCreatorInterface::class);
        $this->directoryCleaner = $this->createMock(DirectoryCleanerInterface::class);
    }

    public function testRender(): void
    {
        $template = 'course/answers.html.twig';

        $loader = new ArrayLoader([
            $template => '<p>TEMPLATE</p>',
        ]);

        $twig = new Environment($loader);

        $renderer = new AnswersToPDFRenderer(
            $twig,
            $this->fileSystemPath,
            $this->wrapper,
            $this->fileSaver,
            $this->directoryCreator,
            $this->directoryCleaner
        );

        $this->directoryCleaner->expects(self::once())
            ->method('cleanDirectory')
            ->with(self::equalTo('/tmp/phpunit_real_storage/1d71cd68-e737-4eb4-8395-5e8650fa3657'));

        $this->directoryCreator->expects(self::once())
            ->method('createDirectory')
            ->with(self::equalTo('/tmp/phpunit_real_storage/1d71cd68-e737-4eb4-8395-5e8650fa3657'));

        $this->wrapper->expects(self::once())
            ->method('getPdf')
            ->with(self::equalTo('<p>TEMPLATE</p>'))
            ->willReturn(base64_decode('test', strict: true));

        $this->fileSaver->expects(self::once())
            ->method('save')
            ->with(self::equalTo(
                '/tmp/phpunit_real_storage/1d71cd68-e737-4eb4-8395-5e8650fa3657/1d71cd68-e737-4eb4-8395-5e8650fa3657.pdf'
            ));

        $renderer->render([], '1d71cd68-e737-4eb4-8395-5e8650fa3657');
    }
}
