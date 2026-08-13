<?php

declare(strict_types=1);

namespace App\Testing\Command\Add;

use App\Course\Api\CourseApi;
use App\Infrastructure\Doctrine\Flusher;
use App\Testing\Entity\Test\DTO\TicketDTO;
use App\Testing\Entity\Test\Test;
use App\Testing\Entity\Test\TestId;
use App\Testing\Entity\Test\TestRepository;
use App\Testing\Service\SlugGeneratorByCipher;
use DateTimeImmutable;
use DomainException;

final class Handler
{
    public function __construct(
        private readonly CourseApi $courseApi,
        private readonly SlugGeneratorByCipher $slugGenerator,
        private readonly TestRepository $tests,
        private readonly Flusher $flusher
    ) {}

    public function handle(Command $command): void
    {
        $slug = $this->slugGenerator->generate($command->cipher);

        if ($this->tests->hasBySlug($slug)) {
            throw new DomainException('Test with slug already exists.');
        }

        $allQuestionIds = [];

        foreach ($command->courseIds as $courseId) {
            $courseQuestions = $this->courseApi->getQuestionIds($courseId);
            $allQuestionIds = array_merge($allQuestionIds, $courseQuestions);
        }

        $allQuestionIds = array_unique($allQuestionIds);
        shuffle($allQuestionIds);

        /** @var TicketDTO[] $tickets */
        $tickets = [];
        $chunks = array_chunk($allQuestionIds, $command->numberQuestionsInTicket);
        foreach ($chunks as $index => $chunk) {
            if ($index >= $command->numberOfTickets) {
                break;
            }

            $tickets[] = new TicketDTO(
                $index + 1,
                $chunk
            );
        }

        $test = new Test(
            TestId::generate(),
            $command->name,
            $command->cipher,
            $command->description,
            $command->allowedMistakes,
            $command->courseIds,
            $tickets,
            $slug,
            new DateTimeImmutable()
        );

        $this->tests->add($test);

        $this->flusher->flush();
    }
}
