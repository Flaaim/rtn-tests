<?php

declare(strict_types=1);

namespace App\Course\Test\Builder;

use App\Course\Entity\Course\Course;
use App\Course\Entity\Course\CourseId;
use DateTimeImmutable;

final class CourseBuilder
{
    private CourseId $courseId;
    private string $name;
    private array $questions;
    private DateTimeImmutable $createdAt;
    private string $cipher;
    private bool $isProtected = false;

    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
    ) {
        $this->courseId = new CourseId('e2cbb4cd-c259-4ac4-80da-728643035370');
        $this->name = 'Course name';
        $this->questions = [];
        $this->createdAt = new DateTimeImmutable();
        $this->cipher = 'ОТ 201.18';
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function withCourseId(CourseId $courseId): self
    {
        $clone = clone $this;
        $clone->courseId = $courseId;
        return $clone;
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function withName(string $name): self
    {
        $clone = clone $this;
        $clone->name = $name;
        return $clone;
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function withQuestions(array $questions): self
    {
        $clone = clone $this;
        $clone->questions = $questions;
        return $clone;
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function withCreatedAt(DateTimeImmutable $createdAt): self
    {
        $clone = clone $this;
        $clone->createdAt = $createdAt;
        return $clone;
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function withCipher(string $cipher): self
    {
        $clone = clone $this;
        $clone->cipher = $cipher;
        return $clone;
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function protect(): self
    {
        $clone = clone $this;
        $clone->isProtected = true;
        return $clone;
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function build(): Course
    {
        $course = new Course(
            $this->courseId,
            $this->name,
            $this->questions,
            $this->createdAt,
            $this->cipher,
        );

        if ($this->isProtected) {
            $course->protect();
        }
        $course->releaseEvents();
        return $course;
    }
}
