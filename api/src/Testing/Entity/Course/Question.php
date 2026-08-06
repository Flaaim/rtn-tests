<?php

declare(strict_types=1);

namespace App\Testing\Entity\Course;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'questions')]
final class Question
{
    /**
     * @param Answer[] $answers
     */
    /** @psalm-suppress PropertyNotSetInConstructor */
    #[ORM\ManyToOne(targetEntity: Course::class, inversedBy: 'questions')]
    #[ORM\JoinColumn(name: 'course_id', referencedColumnName: 'course_id', nullable: false, onDelete: 'RESTRICT')]
    private Course $course;

    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'string', unique: true)]
        private string $id,
        #[ORM\Column(type: 'string', length: 512)]
        private string $text,
        #[ORM\Column(type: 'string', length: 255)]
        private string $questionImg,
        #[ORM\Column(type: Types::JSON, options: ['jsonb' => true])]
        private array $answers,
    ) {}

    public static function fromDraft(array $data): self
    {
        $answers = array_map(static fn (array $answerData) => Answer::fromArray($answerData), $data['answers']);
        return new self(
            id: $data['id'],
            text: $data['text'],
            questionImg: $data['questionImg'],
            answers: $answers
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getQuestionImg(): string
    {
        return $this->questionImg;
    }

    /**
     * @return Answer[]
     */
    public function getAnswers(): array
    {
        if (isset($this->answers[0]) && \is_array($this->answers[0])) {
            $this->answers = array_map(
                static fn (array $answerData) => Answer::fromArray($answerData),
                $this->answers
            );
        }
        return $this->answers;
    }

    public function replaceQuestionImg(string $imgPath): void
    {
        $this->questionImg = $imgPath;
    }

    public function getCourse(): Course
    {
        return $this->course;
    }

    public function appendCourse(Course $course): void
    {
        $this->course = $course;
    }

    public function markAnswersAsUpdated(): void
    {
        $this->answers = array_values($this->answers);
    }
}
