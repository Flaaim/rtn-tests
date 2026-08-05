<?php

declare(strict_types=1);

namespace App\Testing\Entity;

use App\SharedDomain\AggregateRoot;
use App\SharedDomain\Event\EventTrait;
use App\Testing\Event\CourseCreated;
use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;

final class Course implements AggregateRoot
{
    use EventTrait;

    public function __construct(
        private CourseId $courseId,
        private string $name,
        private Collection $questions,
        private DateTimeImmutable $createdAt,
        private CourseStatus $status,
    ) {
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

    public function getStatus(): CourseStatus
    {
        return $this->status;
    }
}
