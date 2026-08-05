<?php

declare(strict_types=1);

namespace App\Testing\Service;

use App\SharedDomain\Filesystem\FileSystemPathInterface;
use App\Testing\Entity\Answer;
use App\Testing\Entity\Question;
use App\Testing\Service\Downloader\DirectoryCreatorInterface;
use App\Testing\Service\Downloader\FileDownloaderInterface;
use App\Testing\Service\Downloader\FilenameGeneratorInterface;

final class CourseMediaDownloader
{
    public function __construct(
        private readonly FileDownloaderInterface $fileDownloader,
        private readonly FilenameGeneratorInterface $filenameGenerator,
        private readonly FileSystemPathInterface $fileSystemPath,
        private readonly DirectoryCreatorInterface $directoryCreator,
    ) {}

    public function downloadMedia(array $questions, string $relativePathDir): array
    {
        $questionsDir = $this->fileSystemPath->getValue() . \DIRECTORY_SEPARATOR . $relativePathDir;
        $this->directoryCreator->createDirectory($questionsDir);

        return array_map(function (Question $question) use ($relativePathDir, $questionsDir): Question {
            if ('' !== $question->getQuestionImg()) {
                $filename = $this->filenameGenerator->generateFilename($question->getQuestionImg());
                $filePath = $questionsDir . \DIRECTORY_SEPARATOR . $filename;
                $absoluteFilePath = $relativePathDir . \DIRECTORY_SEPARATOR . $filename;

                $this->fileDownloader->download($question->getQuestionImg(), $filePath);
                $question->replaceQuestionImg($absoluteFilePath);
            }

            $answers = array_map(function (Answer $answer) use ($relativePathDir): Answer {
                if ('' !== $answer->getAnswerImg()) {
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
                return $answer;
            }, $question->getAnswers());
            $question->replaceAnswers($answers);
            return $question;
        }, $questions);
    }
}
