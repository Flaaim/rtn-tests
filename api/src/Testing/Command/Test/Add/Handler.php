<?php

declare(strict_types=1);

namespace App\Testing\Command\Test\Add;

use App\Infrastructure\Doctrine\Flusher;
use App\Testing\Entity\Test\Settings;
use App\Testing\Entity\Test\Test;
use App\Testing\Entity\Test\TestId;
use App\Testing\Entity\Test\TestRepository;
use App\Testing\Interfaces\CourseInterface;
use App\Testing\Service\SlugGenerator\SlugGeneratorByCipher;
use DateTimeImmutable;
use DomainException;

final readonly class Handler
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private CourseInterface $course,
        private SlugGeneratorByCipher $slugGenerator,
        private TestRepository $tests,
        private Flusher $flusher
    ) {}

    public function handle(Command $command): void
    {
        $slug = $this->slugGenerator->generate($command->cipher);

        if ($this->tests->hasBySlug($slug)) {
            throw new DomainException('Test with slug already exists.');
        }

        $settings = new Settings(
            $command->numberOfTickets,
            $command->numberQuestionsInTicket,
            $command->allowedMistakes
        );

        $allQuestionIds = [];

        foreach ($command->courseIds as $courseId) {
            $courseQuestions = $this->course->getQuestionIds($courseId);
            $allQuestionIds = array_merge($allQuestionIds, $courseQuestions);
        }

        $allQuestionIds = array_unique($allQuestionIds);
        shuffle($allQuestionIds);

        $test = new Test(
            TestId::generate(),
            $command->categoryId,
            $command->name,
            $command->cipher,
            $command->description,
            $command->courseIds,
            $allQuestionIds,
            $slug,
            new DateTimeImmutable(),
            $settings
        );

        $this->tests->add($test);

        $this->flusher->flush();
    }
}
