<?php

declare(strict_types=1);

namespace App\Testing\Command\Attempt\DemoLaunch;

use App\Course\Api\Course\GetQuestions\QueryHandlerApi;
use App\Infrastructure\Doctrine\Flusher;
use App\Testing\Entity\Attempt\Attempt;
use App\Testing\Entity\Attempt\AttemptId;
use App\Testing\Entity\Attempt\AttemptRepository;
use App\Testing\Entity\Attempt\Status;
use App\Testing\Entity\Test\TestId;
use App\Testing\Entity\Test\TestRepository;
use DateTimeImmutable;
use DomainException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(method: 'handle')]
final readonly class Handler
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private TestRepository $tests,
        private AttemptRepository $attempts,
        private QueryHandlerApi $queryHandler,
        private Flusher $flusher,
    ) {}

    public function handle(Command $command): string
    {
        $demoCountAttempts = $this->attempts->countByUserId($command->guestId);

        if ($demoCountAttempts >= 2) {
            throw new DomainException('Демо-доступ исчерпан. Пожалуйста, зарегистрируйтесь.');
        }

        $test = $this->tests->get(new TestId($command->testId));

        $tickets = $test->getTickets();
        $index = $command->ticketNumber - 1;
        $ticket = $tickets[$index] ?? null;

        if (null === $ticket) {
            throw new DomainException('Invalid ticket number.');
        }

        $questionsSnapshot = $this->queryHandler->getQuestions($ticket->questionIds);

        if (empty($questionsSnapshot)) {
            throw new DomainException('No questions found in course.');
        }

        $attempt = new Attempt(
            AttemptId::generate(),
            $test->getId()->getValue(),
            $command->guestId,
            Status::inProgress(),
            new DateTimeImmutable(),
            $ticket->number,
            $questionsSnapshot
        );

        $this->attempts->add($attempt);

        $this->flusher->flush();

        return $attempt->getId()->getValue();
    }
}
