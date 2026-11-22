<?php

declare(strict_types=1);

namespace App\Course\Entity\Course;

use App\Course\Event\CourseProcessed;
use App\Course\Event\CourseProtected;
use App\Course\Event\CourseQuestionsUpdated;
use App\Course\Event\CourseRendered;
use App\Course\Event\CourseScheduledToRender;
use App\Course\Event\CourseUnprotected;
use App\SharedDomain\AggregateRoot;
use App\SharedDomain\Event\EventTrait;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'courses')]
final class Course implements AggregateRoot
{
    use EventTrait;
    #[ORM\Column(type: 'course_status')]
    private Status $status;
    #[ORM\OneToMany(targetEntity: Question::class, mappedBy: 'course', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $questions;

    public function __construct(
        #[ORM\Id]
        #[ORM\Column(name: 'course_id', type: 'course_id', unique: true)]
        private CourseId $courseId,
        #[ORM\Column(type: 'string', length: 255)]
        private string $name,
        array $questions,
        #[ORM\Column(type: 'datetime_immutable')]
        private DateTimeImmutable $createdAt,
        #[ORM\Column(type: 'string', length: 255)]
        private string $cipher,
        #[ORM\Column(type: 'boolean', options: ['default' => false])]
        private bool $isProtected = false,
    ) {
        $this->questions = new ArrayCollection($questions);
        foreach ($questions as $question) {
            $question->appendCourse($this);
        }
        $this->status = Status::processing();
        $this->recordEvent(new CourseProcessed($this->courseId->getValue()));
    }

    public function getCourseId(): CourseId
    {
        return $this->courseId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getQuestions(): array
    {
        return $this->questions->toArray();
    }

    public function getCipher(): string
    {
        return $this->cipher;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function updateStatus(Status $status): void
    {
        $this->status = $status;
    }

    public function markAsCreated(): void
    {
        $this->status = Status::created();
        $this->recordEvent(
            new CourseScheduledToRender($this->courseId->getValue())
        );
    }

    public function markAsRendered(string $file, string $amount): void
    {
        $this->status = Status::completed();
        $this->recordEvent(
            new CourseRendered(
                $this->cipher,
                $file,
                $this->courseId->getValue(),
                $amount
            )
        );
    }

    public function addQuestions(array $newQuestions): void
    {
        $this->status = Status::processing();

        foreach ($this->questions as $existingQuestion) {
            $this->questions->removeElement($existingQuestion);
        }
        $this->questions->clear();
        foreach ($newQuestions as $question) {
            $question->appendCourse($this);
            $this->questions->add($question);
        }
        $this->recordEvent(new CourseProcessed($this->courseId->getValue()));
        $this->recordEvent(new CourseQuestionsUpdated($this->courseId->getValue()));
    }

    public function rename(?string $newName = null, ?string $newCipher = null): void
    {
        if (null !== $newName) {
            $this->name = $newName;
        }

        if (null !== $newCipher) {
            $this->cipher = $newCipher;
        }
    }

    public function isProtected(): bool
    {
        return $this->isProtected;
    }

    public function protect(): void
    {
        if ($this->isProtected()) {
            return;
        }
        $this->isProtected = true;
        $this->recordEvent(new CourseProtected(
            $this->courseId->getValue(),
            $this->cipher
        ));
    }

    public function unprotect(): void
    {
        if (!$this->isProtected()) {
            return;
        }
        $this->isProtected = false;
        $this->recordEvent(
            new CourseUnprotected(
                $this->courseId->getValue(),
                $this->cipher
            )
        );
    }

    public function getAggregateId(): string
    {
        return $this->courseId->getValue();
    }
}
