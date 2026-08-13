<?php

declare(strict_types=1);

namespace App\Testing\Command\Add;

use App\Course\Api\CourseApi;
use App\Infrastructure\Doctrine\Flusher;
use App\Testing\Entity\DTO\TicketDTO;
use App\Testing\Entity\Status;
use App\Testing\Entity\Test;
use App\Testing\Entity\TestId;
use App\Testing\Entity\TestRepository;
use App\Testing\Service\SlugGeneratorByCipher;
use DateTimeImmutable;

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
            Status::inactive(),
            $this->slugGenerator->generate($command->cipher),
            new DateTimeImmutable()
        );

        $this->tests->add($test);

        $this->flusher->flush();
    }
}
