<?php

declare(strict_types=1);

namespace App\Testing\Command\Attempt\Launch;

use App\Infrastructure\Doctrine\Flusher;
use App\Subscription\Api\SubscriptionApi;
use App\Testing\Entity\Attempt\Attempt;
use App\Testing\Entity\Attempt\AttemptId;
use App\Testing\Entity\Attempt\AttemptRepository;
use App\Testing\Entity\Attempt\Status;
use App\Testing\Entity\Test\TestId;
use App\Testing\Entity\Test\TestRepository;
use App\Testing\Event\Attempt\TimeoutAttemptCommand;
use App\Testing\Interfaces\CourseInterface;
use DateTimeImmutable;
use DomainException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

#[AsMessageHandler(method: 'handle')]
final readonly class Handler
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private TestRepository $tests,
        private AttemptRepository $attempts,
        private CourseInterface $course,
        private MessageBusInterface $messageBus,
        private Flusher $flusher,
        private SubscriptionApi $subscriptionApi,
    ) {}

    public function handle(Command $command): string
    {
        $this->subscriptionApi->ensureHasAccess($command->userId);

        $processedAttempt = $this->attempts->findUserProcessedAttempt(
            $command->userId,
            $command->testId,
            $command->ticketNumber
        );

        if (null !== $processedAttempt) {
            return $processedAttempt->getId()->getValue();
        }

        $test = $this->tests->get(new TestId($command->testId));

        $tickets = $test->getTickets();
        $index = $command->ticketNumber - 1;
        $ticket = $tickets[$index] ?? null;

        if (null === $ticket) {
            throw new DomainException('Invalid ticket number.');
        }

        $questionsSnapshot = $this->course->getQuestions($ticket->questionIds);

        if (empty($questionsSnapshot)) {
            throw new DomainException('No questions found in course.');
        }

        $attempt = new Attempt(
            AttemptId::generate(),
            $test->getId()->getValue(),
            $command->userId,
            Status::inProgress(),
            new DateTimeImmutable(),
            $ticket->number,
            $questionsSnapshot
        );

        $this->attempts->add($attempt);

        $this->flusher->flush();

        $delayInMilliseconds = 60 * 60 * 1000;

        $this->messageBus->dispatch(
            new TimeoutAttemptCommand($attempt->getId()->getValue()),
            [new DelayStamp($delayInMilliseconds)]
        );

        return $attempt->getId()->getValue();
    }
}
