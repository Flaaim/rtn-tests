<?php

declare(strict_types=1);

namespace App\Course\Service\FileRenderer;

use App\Course\Service\Downloader\DirectoryCleanerInterface;
use App\Course\Service\Downloader\DirectoryCreatorInterface;
use App\SharedDomain\Filesystem\FileSystemPathInterface;
use Nucleos\DompdfBundle\Wrapper\DompdfWrapperInterface;
use Twig\Environment;

final readonly class AnswersToPDFRenderer implements FileRendererInterface
{
    public const string TEMPLATE = 'course/answers.html.twig';

    public function __construct(
        private Environment $twig,
        private FileSystemPathInterface $coursePath,
        private DompdfWrapperInterface $wrapper,
        private FileSaverInterface $fileSaver,
        private DirectoryCreatorInterface $directoryCreator,
        private DirectoryCleanerInterface $directoryCleaner,
    ) {}

    public function render(array $questions, string $courseId): void
    {
        $dir = $this->coursePath->getValue() . \DIRECTORY_SEPARATOR . $courseId;

        $this->directoryCleaner->cleanDirectory($dir);
        $this->directoryCreator->createDirectory($dir);

        $content = $this->twig->render(self::TEMPLATE, ['questions' => $questions]);

        $output = $this->wrapper->getPdf($content);

        $file = $dir . \DIRECTORY_SEPARATOR . $courseId . '.pdf';

        $this->fileSaver->save($file, $output);
    }
}
