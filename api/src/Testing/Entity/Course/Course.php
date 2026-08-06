<?php

declare(strict_types=1);

namespace App\Testing\Entity\Course;

use App\SharedDomain\AggregateRoot;
use App\SharedDomain\Event\EventTrait;
use App\Testing\Event\CourseCreated;
use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'courses')]
final class Course implements AggregateRoot
{
    use EventTrait;
    #[ORM\Column(type: 'course_status')]
    private Status $status;

    public function __construct(
        #[ORM\Id]
        #[ORM\Column(name: 'course_id', type: 'course_id', unique: true)]
        private CourseId $courseId,
        #[ORM\Column(type: 'string', length: 255)]
        private string $name,
        #[ORM\OneToMany(targetEntity: Question::class, mappedBy: 'course', cascade: ['persist', 'remove'], orphanRemoval: true)]
        /** @var Question[] */
        private Collection $questions,
        #[ORM\Column(type: 'datetime_immutable')]
        private DateTimeImmutable $createdAt
    ) {
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

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function updateStatus(Status $status): void
    {
        $this->status = $status;
    }

    public function addQuestions(Collection $newQuestions): void
    {
        $this->questions->clear();
        foreach ($newQuestions as $question) {
            $question->appendCourse($this);
            $this->questions->add($question);
        }
    }
}
