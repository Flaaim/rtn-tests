<?php

declare(strict_types=1);

namespace App\Testing\Test\Unit\Service;

use App\SharedDomain\Filesystem\FileSystemPathInterface;
use App\SharedDomain\Filesystem\InMemoryFileSystemPath;
use App\Testing\Entity\Course\Answer;
use App\Testing\Entity\Course\Question;
use App\Testing\Service\CourseMediaDownloader;
use App\Testing\Service\Downloader\DirectoryCleanerInterface;
use App\Testing\Service\Downloader\DirectoryCreatorInterface;
use App\Testing\Service\Downloader\FileDownloaderInterface;
use App\Testing\Service\Downloader\FilenameGeneratorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class CourseMediaDownloaderTest extends TestCase
{
    /** @var FileDownloaderInterface&MockObject */
    private FileDownloaderInterface $fileDownloader;
    /** @var FilenameGeneratorInterface&MockObject */
    private FilenameGeneratorInterface $filenameGenerator;

    private FileSystemPathInterface $fileSystemPath;
    /** @var DirectoryCreatorInterface&MockObject */
    private DirectoryCreatorInterface $directoryCreator;
    /** @var DirectoryCleanerInterface&MockObject */
    private DirectoryCleanerInterface $directoryCleaner;

    protected function setUp(): void
    {
        parent::setUp();
        $this->filenameGenerator = $this->createMock(FilenameGeneratorInterface::class);
        $this->fileSystemPath = InMemoryFileSystemPath::createReal();
        $this->fileDownloader = $this->createMock(FileDownloaderInterface::class);
        $this->directoryCreator = $this->createMock(DirectoryCreatorInterface::class);
        $this->directoryCleaner = $this->createMock(DirectoryCleanerInterface::class);
    }

    public function testAddNew(): void
    {
        $mediaDownloader = new CourseMediaDownloader(
            $this->fileDownloader,
            $this->filenameGenerator,
            $this->fileSystemPath,
            $this->directoryCreator,
            $this->directoryCleaner
        );

        $relativePath = '12345';
        $callCount = 1;
        $this->directoryCleaner->expects(self::once())->method('cleanDirectory');

        $this->directoryCreator->expects(self::atLeast(2))
            ->method('createDirectory')
            ->willReturnCallback(function (string $param) use (&$callCount, $relativePath): bool {
                if (1 === $callCount) {
                    self::assertEquals($this->fileSystemPath->getValue() . \DIRECTORY_SEPARATOR . $relativePath, $param);
                } elseif (2 === $callCount) {
                    self::assertEquals(
                        $this->fileSystemPath->getValue() .
                        \DIRECTORY_SEPARATOR . $relativePath .
                        \DIRECTORY_SEPARATOR . '234a678461729b0a6d96375a55c349bf',
                        $param
                    );
                }
                ++$callCount;
                return true;
            });

        $sourceQuestions = $this->getQuestions();

        $this->filenameGenerator->expects(self::atLeast(3))
            ->method('generateFilename')
            ->willReturnCallback(static function () {
                static $index  = 0;
                $files = ['file1.jpg', 'answerfile1.jpg', 'file2.jpg'];
                $value = $files[$index] ?? null;
                ++$index;

                return $value;
            });

        $this->fileDownloader->expects(self::atLeast(3))->method('download');

        $mediaDownloader->downloadMedia($sourceQuestions, $relativePath);
        /** @var Question[] $questions */
        self::assertEquals('12345/file1.jpg', $sourceQuestions[0]->getQuestionImg());
        self::assertEquals('12345/file2.jpg', $sourceQuestions[1]->getQuestionImg());
        self::assertEquals('12345/234a678461729b0a6d96375a55c349bf/answerfile1.jpg', $sourceQuestions[0]->getAnswers()[0]->getAnswerImg());
    }

    private function getQuestions(): array
    {
        return [
            new Question(
                '7cedc5c2-e9eb-4986-83c2-d6795c2e3ff6',
                'Вопрос 1',
                'https://olimpoks.hydroschool.ru/QuestionImages/c92099/9fef1bcf-9c6c-4010-a670-3dc105abc574/10/1.jpg',
                [
                    Answer::fromArray(
                        [
                            'id' => '234a678461729b0a6d96375a55c349bf',
                            'text' => 'Травмы живота и таза',
                            'isCorrect' => true,
                            'answerImg' => 'https://olimpoks.hydroschool.ru/QuestionImages/92cdb7c1-efee-4790-bed7-f194f02da614/9/3.jpg',
                        ]
                    ),
                ]
            ),
            new Question(
                'e6616f72-0af2-4510-96bb-9ce92265a712',
                'Вопрос 2',
                'https://olimpoks.hydroschool.ru/QuestionImages/c92192/145d3d30-5398-478d-bbb9-82c820f8ac1f/9/1.jpg',
                []
            ),
        ];
    }
}
