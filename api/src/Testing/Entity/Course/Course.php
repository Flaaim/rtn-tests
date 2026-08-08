<?php

declare(strict_types=1);

namespace App\Testing\Entity\Course;

use App\SharedDomain\AggregateRoot;
use App\SharedDomain\Event\EventTrait;
use App\Testing\Event\CourseCreated;
use App\Testing\Event\CourseUpdated;
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
    ) {
        $this->questions = new ArrayCollection($questions);
        foreach ($questions as $question) {
            $question->appendCourse($this);
        }
        $this->status = Status::processing();
        $this->recordEvent(new CourseCreated($this->courseId->getValue()));
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
        $this->recordEvent(new CourseUpdated($this->courseId->getValue()));
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
}
