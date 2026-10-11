<?php

declare(strict_types=1);

namespace App\Testing\Entity\Attempt;

use App\SharedDomain\AggregateRoot;
use App\SharedDomain\Event\EventTrait;
use App\Testing\Entity\Attempt\DTO\QuestionDTO;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use DomainException;

#[ORM\Entity]
#[ORM\Table(name: 'attempts')]
final class Attempt implements AggregateRoot
{
    use EventTrait;
    #[ORM\OneToMany(targetEntity: Answer::class, mappedBy: 'attempt', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $answers;

    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'attempt_id', unique: true)]
        private AttemptId $id,
        #[ORM\Column(type: 'string')]
        private string $testId,
        #[ORM\Column(type: 'string')]
        private string $userId,
        #[ORM\Column(type: 'attempt_status')]
        private Status $status,
        #[ORM\Column(type: 'datetime_immutable')]
        private DateTimeImmutable $startedAt,
        #[ORM\Column(type: 'integer', nullable: true)]
        private ?int $ticketNumber,
        #[ORM\Column(type: Types::JSON, options: ['jsonb' => true])]
        private array $questionsSnapshot,
        #[ORM\Column(type: 'integer')]
        private int $score = 0,
        #[ORM\Column(type: 'integer')]
        private int $mistakes = 0,
        #[ORM\Column(type: 'datetime_immutable', nullable: true)]
        private ?DateTimeImmutable $finishedAt = null,
    ) {
        $this->answers = new ArrayCollection();
    }

    public function getId(): AttemptId
    {
        return $this->id;
    }

    public function getTestId(): string
    {
        return $this->testId;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function getStartedAt(): DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function getTicketNumber(): ?int
    {
        return $this->ticketNumber;
    }

    public function getQuestionSnapshot(): array
    {
        if (isset($this->questionsSnapshot[0]) && \is_array($this->questionsSnapshot[0])) {
            $this->questionsSnapshot = array_map(
                static fn (array $questionData) => QuestionDTO::fromArray($questionData),
                $this->questionsSnapshot
            );
        }
        return $this->questionsSnapshot;
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function getMistakes(): int
    {
        return $this->mistakes;
    }

    public function getFinishedAt(): ?DateTimeImmutable
    {
        return $this->finishedAt;
    }

    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function isInProgress(): bool
    {
        return Status::STATUS_IN_PROGRESS === $this->status->getValue();
    }

    public function submitAnswer(string $questionId, array $selectedAnswersIds): void
    {
        if (!$this->isInProgress()) {
            throw new DomainException('Cannot submit answers for a completed attempt.');
        }

        $question = $this->findQuestionInSnapshot($questionId);

        if (empty($question)) {
            throw new DomainException('Question not found in this attempt.');
        }

        $isCorrect = $this->validateAnswer($question, $selectedAnswersIds);

        $answer = new Answer(
            AnswerId::generate(),
            $questionId,
            $selectedAnswersIds,
            $isCorrect
        );

        $answer->appendAttempt($this);

        $this->answers->add($answer);

        if ($answer->isCorrect()) {
            ++$this->score;
        } else {
            ++$this->mistakes;
        }
    }

    public function finish(int $allowedMistakes): void
    {
        if (!$this->isInProgress()) {
            throw new DomainException('Attempt is already finished.');
        }
        $this->finishedAt = new DateTimeImmutable();
        if ($this->mistakes > $allowedMistakes) {
            $this->status = Status::failed();
        } else {
            $this->status = Status::passed();
        }
    }

    public function finishByTimeout(): void
    {
        if (!$this->isInProgress()) {
            throw new DomainException('Attempt is already finished.');
        }
        $this->finishedAt = new DateTimeImmutable();
        $this->status = Status::timeout();
    }

    public function assignToUser(string $userId): void
    {
        $this->userId = $userId;
    }

    private function findQuestionInSnapshot(string $questionId): ?QuestionDTO
    {
        return array_find($this->getQuestionSnapshot(), static fn ($snapshot) => $snapshot->id === $questionId);
    }

    private function validateAnswer(QuestionDTO $question, array $selectedIds): bool
    {
        if (empty($selectedIds)) {
            return false;
        }

        $selectedAnswerIds = array_values($selectedIds);

        if (QuestionForm::SINGLE_CHOICE->value === $question->form) {
            if (\count($selectedAnswerIds) > 1) {
                return false;
            }
            foreach ($question->answers as $answer) {
                if ($answer['id'] === $selectedAnswerIds[0] && true === $answer['isCorrect']) {
                    return true;
                }
            }
            return false;
        }
        if (QuestionForm::MULTIPLE_CHOICE->value === $question->form) {
            $totalCorrectAnswers = 0;
            $selectedCorrectAnswers = 0;

            foreach ($question->answers as $answer) {
                if (true === $answer['isCorrect']) {
                    ++$totalCorrectAnswers;
                }

                if (\in_array($answer['id'], $selectedAnswerIds, true) && true === $answer['isCorrect']) {
                    ++$selectedCorrectAnswers;
                }
            }

            return $selectedCorrectAnswers === $totalCorrectAnswers && \count($selectedAnswerIds) === $totalCorrectAnswers;
        }

        if (QuestionForm::SEQUENCE->value === $question->form) {
            return array_column($question->answers, 'id') === $selectedAnswerIds;
        }

        if (QuestionForm::MATCHING->value === $question->form) {
            $rightColumn = $question->answers['right'] ?? [];
            return array_column($rightColumn, 'id') === $selectedAnswerIds;
        }

        return true;
    }
}
