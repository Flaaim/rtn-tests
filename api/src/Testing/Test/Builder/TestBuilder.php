<?php

declare(strict_types=1);

namespace App\Testing\Test\Builder;

use App\Testing\Entity\Test\Settings;
use App\Testing\Entity\Test\Test;
use App\Testing\Entity\Test\TestId;
use DateTimeImmutable;
use DomainException;

final class TestBuilder
{
    private TestId $id;
    private string $name;
    private string $description;
    private string $cipher;
    private array $courseIds;
    private array $questionIds;
    private ?string $slug = null;
    private DateTimeImmutable $createdAt;
    private Settings $settings;
    private bool $active = false;

    public function __construct(
    ) {
        $this->id = new TestId('6ed7c3cb-b8ea-4615-8cfe-67b389a2d193');
        $this->name = 'Первая помощь';
        $this->cipher = 'ОТ 201.18';
        $this->description = 'Test description';
        $this->courseIds = ['0121b081-c461-42f0-b8ec-a4632a64faea'];
        $this->questionIds = ['7645fc15-26aa-4c3c-a5a4-9724c9f5f455', '48b75db2-113c-4ae7-becb-7bc830016c61'];
        $this->slug = 'ot201';
        $this->createdAt = new DateTimeImmutable();
        $this->settings = new Settings(10, 10, 2);
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function withId(TestId $id): self
    {
        $clone = clone $this;
        $clone->id = $id;
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
    public function withCipher(string $cipher): self
    {
        $clone = clone $this;
        $clone->cipher = $cipher;
        $clone->slug = null;
        return $clone;
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function withDescription(string $description): self
    {
        $clone = clone $this;
        $clone->description = $description;
        return $clone;
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function withSettings(Settings $settings): self
    {
        $clone = clone $this;
        $clone->settings = $settings;
        return $clone;
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function withCourseIds(array $courseIds): self
    {
        $clone = clone $this;
        $clone->courseIds = $courseIds;
        return $clone;
    }

    public function withQuestionIds(array $questionIds): self
    {
        $clone = clone $this;
        $clone->questionIds = $questionIds;
        return $clone;
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function withSlug(string $slug): self
    {
        $clone = clone $this;
        $clone->slug = $slug;
        return $clone;
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function active(): self
    {
        $clone = clone $this;
        $clone->active = true;
        return $clone;
    }

    public function build(): Test
    {
        $currentSlug = $this->slug ?? $this->generateSlug($this->cipher);

        $test = new Test(
            $this->id,
            $this->name,
            $this->cipher,
            $this->description,
            $this->courseIds,
            $this->questionIds,
            $currentSlug,
            $this->createdAt,
            $this->settings,
        );

        if ($this->active) {
            $test->activate();
        }

        $test->releaseEvents();

        return $test;
    }

    private function generateSlug(string $cipher): string
    {
        $value = mb_strtolower($cipher);
        $value = preg_replace('/\..*/', '', $value);

        if (null === $value) {
            throw new DomainException('Slug in builder cannot be empty');
        }

        $result = preg_replace('/[^a-z0-9]+/', '', $value);
        if (null === $result) {
            throw new DomainException('Slug in builder cannot be empty');
        }

        return $result;
    }
}
