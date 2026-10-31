<?php

declare(strict_types=1);

namespace App\Course\Service\FileRenderer;

final readonly class FileRendererFacade
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private DirectoryPreparerInterface $directoryPreparer,
        private FileRendererInterface $answersToPDFRenderer,
        private FileSaverInterface $fileSaver,
    ) {}

    public function renderAndSave(string $courseId, array $questions): string
    {
        $directory = $this->directoryPreparer->prepare($courseId);

        $content = $this->answersToPDFRenderer->render($questions, $courseId);

        $destinationFilePath = $directory . \DIRECTORY_SEPARATOR . $courseId . '.pdf';
        $this->fileSaver->save($destinationFilePath, $content);

        return basename($directory) . \DIRECTORY_SEPARATOR . $courseId . '.pdf';
    }
}
