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
    #[ORM\JoinColumn(name: 'course_id', referencedColumnName: 'course_id', nullable: false, onDelete: 'CASCADE')]
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
        #[ORM\Column(type: 'question_form')]
        private QuestionForm $form
    ) {}

    public static function fromDraft(array $data): self
    {
        $answers = array_map(static fn (array $answerData) => Answer::fromArray($answerData), $data['answers']);

        $form = self::detectForm($data['text'], $answers);

        return new self(
            id: $data['id'],
            text: $data['text'],
            questionImg: $data['questionImg'],
            answers: $answers,
            form: $form
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

    public function getForm(): QuestionForm
    {
        return $this->form;
    }

    public function resolveForm(QuestionForm $form): void
    {
        $this->form = $form;
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

    private static function detectForm(string $text, array $answers): QuestionForm
    {
        $totalAnswers = \count($answers);
        $correctedAnswers = 0;

        foreach ($answers as $answer) {
            if (true === $answer->isCorrect()) {
                ++$correctedAnswers;
            }
        }

        if ($correctedAnswers === $totalAnswers && $totalAnswers > 0) {
            if (TrapPhrases::isSequence($text)) {
                return QuestionForm::sequence();
            }

            if (TrapPhrases::isMatching($text)) {
                return QuestionForm::matching();
            }
        }

        if (1 === $correctedAnswers) {
            return QuestionForm::singleChoice();
        }

        if ($correctedAnswers > 1) {
            return QuestionForm::multipleChoice();
        }

        return QuestionForm::singleChoice();
    }
}
