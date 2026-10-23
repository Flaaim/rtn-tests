<?php

declare(strict_types=1);

namespace App\Testing\Command\Attempt\LaunchMistakes;

use App\Infrastructure\Doctrine\Flusher;
use App\Subscription\Api\SubscriptionApi;
use App\Testing\Entity\Attempt\Attempt;
use App\Testing\Entity\Attempt\AttemptId;
use App\Testing\Entity\Attempt\AttemptRepository;
use App\Testing\Entity\Attempt\Status;
use App\Testing\Event\Attempt\TimeoutAttemptCommand;
use App\Testing\Interfaces\CourseInterface;
use App\Testing\Query\Attempt\AttemptFetcherInterface;
use DateTimeImmutable;
use DomainException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

final readonly class Handler
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private AttemptFetcherInterface $attemptsFetcher,
        private AttemptRepository $attempts,
        private SubscriptionApi $subscriptionApi,
        private CourseInterface $course,
        private Flusher $flusher,
        private MessageBusInterface $messageBus,
    ) {}

    public function handle(Command $command): string
    {
        $this->subscriptionApi->ensureHasAccess($command->userId);

        $failedQuestionIds = $this->attemptsFetcher->findFailedQuestionsIds($command->userId, $command->testId);

        if (empty($failedQuestionIds)) {
            throw new DomainException('У вас пока нет ошибок для проработки. Отличная работа!');
        }

        shuffle($failedQuestionIds);
        $selectedIds = \array_slice($failedQuestionIds, 0, 20);

        $questionsSnapshot = $this->course->getQuestions($selectedIds);

        if (empty($questionsSnapshot)) {
            throw new DomainException('No questions found in course.');
        }

        $attempt = new Attempt(
            AttemptId::generate(),
            $command->testId,
            $command->userId,
            Status::inProgress(),
            new DateTimeImmutable(),
            0,
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
