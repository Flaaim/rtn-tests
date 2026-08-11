<?php

declare(strict_types=1);

namespace App\Course\Service;

use App\Course\Entity\Course\Question;
use App\Course\Service\Downloader\DirectoryCleanerInterface;
use App\Course\Service\Downloader\DirectoryCreatorInterface;
use App\Course\Service\Downloader\FileDownloaderInterface;
use App\Course\Service\Downloader\FilenameGeneratorInterface;
use App\SharedDomain\Filesystem\FileSystemPathInterface;

final class CourseMediaDownloader
{
    public function __construct(
        private readonly FileDownloaderInterface $fileDownloader,
        private readonly FilenameGeneratorInterface $filenameGenerator,
        private readonly FileSystemPathInterface $fileSystemPath,
        private readonly DirectoryCreatorInterface $directoryCreator,
        private readonly DirectoryCleanerInterface $directoryCleaner,
    ) {}

    public function downloadMedia(array $questions, string $relativePathDir): void
    {
        $questionsDir = $this->fileSystemPath->getValue() . \DIRECTORY_SEPARATOR . $relativePathDir;

        $this->directoryCleaner->cleanDirectory($questionsDir);
        $this->directoryCreator->createDirectory($questionsDir);

        /** @var Question[] $questions */
        foreach ($questions as $question) {
            $imgUrl = $question->getQuestionImg();
            if ('' !== $imgUrl && str_starts_with($imgUrl, 'http')) {
                $filename = $this->filenameGenerator->generateFilename($question->getQuestionImg());
                $filePath = $questionsDir . \DIRECTORY_SEPARATOR . $filename;
                $absoluteFilePath = $relativePathDir . \DIRECTORY_SEPARATOR . $filename;

                $this->fileDownloader->download($question->getQuestionImg(), $filePath);
                $question->replaceQuestionImg($absoluteFilePath);
            }
            foreach ($question->getAnswers() as $answer) {
                $answerImg = $answer->getAnswerImg();
                if ('' !== $answerImg && str_starts_with($answerImg, 'http')) {
                    $answersDir = $this->fileSystemPath->getValue() .
                        \DIRECTORY_SEPARATOR . $relativePathDir .
                        \DIRECTORY_SEPARATOR . $answer->getId();

                    $this->directoryCreator->createDirectory($answersDir);
                    $filename = $this->filenameGenerator->generateFilename($answer->getAnswerImg());
                    $filePath = $answersDir . \DIRECTORY_SEPARATOR . $filename;

                    $absoluteFilePath = $relativePathDir .
                        \DIRECTORY_SEPARATOR . $answer->getId() .
                        \DIRECTORY_SEPARATOR . $filename;

                    $this->fileDownloader->download($answer->getAnswerImg(), $filePath);
                    $answer->replaceAnswerImg($absoluteFilePath);
                }
            }
        }
    }
}
