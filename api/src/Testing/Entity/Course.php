<?php

declare(strict_types=1);

namespace App\Testing\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;

final class Course
{
    public function __construct(
        private CourseId $courseId,
        private string $name,
        private Collection $questions,
        private DateTimeImmutable $createdAt,
        private CourseStatus $status,
    ) {}

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
