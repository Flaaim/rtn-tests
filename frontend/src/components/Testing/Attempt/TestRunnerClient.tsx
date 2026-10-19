"use client";

import { AttemptInterface, SubmitAnswerPayload } from "@/interfaces/attempt.interface";
import { useState } from "react";
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from "@/components/ui/card";
import { PUBLIC_ASSETS_URL } from "@/app/api";
import { Button } from "@/components/ui/button";
import { finishAttemptAction, submitAnswerAction } from "@/actions/attempt";
import { SingleChoiceQuestion } from "@/components/Testing/Attempt/SingleChoiceQuestion";
import { MultipleChoiceQuestion } from "@/components/Testing/Attempt/MultipleChoiceQuestion";
import { SequenceQuestion } from "@/components/Testing/Attempt/SequenceQuestion";
import { MatchingQuestion } from "@/components/Testing/Attempt/MatchingQuestion";
import { useRouter } from "next/navigation";
import { toast } from "sonner";

interface TestRunnerClientProps {
  attempt: AttemptInterface;
}

export default function TestRunnerClient({ attempt }: TestRunnerClientProps) {
  const [currentQuestionIndex, setCurrentQuestionIndex] = useState(0);
  const [isSubmitting, setIsSubmitting] = useState(false);

  const questions = attempt.questions || [];
  const currentQuestion = questions[currentQuestionIndex];
  const [currentAnswers, setCurrentAnswers] = useState<string[]>([]);

  const router = useRouter();

  if (!currentQuestion) {
    return <div className="p-8 text-center text-muted-foreground">Вопросы не найдены.</div>;
  }

  const isLastQuestion = currentQuestionIndex === questions.length - 1;

  const hasSelectedAnswer = currentAnswers.length > 0;

  const handleNext = async () => {
    if (!hasSelectedAnswer) {
      toast.error("Пожалуйста, выберите вариант ответа.");
      return;
    }

    setIsSubmitting(true);

    const payload: SubmitAnswerPayload = {
      id: attempt.id,
      questionId: currentQuestion.id,
      selectedAnswersIds: currentAnswers,
    };
    const result = await submitAnswerAction(payload);

    if (!result.ok) {
      toast.error(result.error || "Ошибка при сохранении ответа. Попробуйте еще раз.");
      return;
    }
    setIsSubmitting(false);

    if (!isLastQuestion) {
      setCurrentQuestionIndex((prev) => prev + 1);
      setCurrentAnswers([]);
    } else {
      finishAttemptAction(payload.id);
      router.push(`${payload.id}/result`);
      return;
    }
  };
  const renderQuestionForm = () => {
    switch (currentQuestion.form) {
      case "single_choice":
        return (
          <SingleChoiceQuestion
            question={currentQuestion}
            selectedAnswerId={currentAnswers[0] || ""}
            onAnswerChange={(id) => setCurrentAnswers([id])}
          />
        );
      case "multiple_choice":
        return (
          <MultipleChoiceQuestion
            question={currentQuestion}
            selectedAnswersIds={currentAnswers}
            onAnswerChange={setCurrentAnswers}
          />
        );
      case "sequence":
        return (
          <SequenceQuestion
            question={currentQuestion}
            selectedAnswersIds={currentAnswers}
            onAnswerChange={setCurrentAnswers}
          />
        );

      case "matching":
        return (
          <MatchingQuestion
            question={currentQuestion}
            selectedAnswersIds={currentAnswers}
            onAnswerChange={setCurrentAnswers}
          />
        );
      default:
        return <div className="text-destructive">Неизвестный тип вопроса</div>;
    }
  };

  const ticketNumber =
    attempt.ticketNumber !== 0 ? `Билет № ${attempt.ticketNumber}` : "Работа над ошибками";
  return (
    <div className="max-w-3xl mx-auto space-y-6">
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-bold">{ticketNumber}</h1>
        Вопрос {currentQuestionIndex + 1} из {questions.length}
      </div>
      <Card>
        <CardHeader>
          <CardTitle className="leading-relaxed">{currentQuestion.text}</CardTitle>
          {currentQuestion.questionImg && (
            <div className="mt-4 border rounded-md overflow-hidden relative inline-block">
              {/* eslint-disable-next-line @next/next/no-img-element */}
              <img
                src={`${PUBLIC_ASSETS_URL}${process.env.NEXT_PUBLIC_QUESTION_IMAGES}${currentQuestion.questionImg}`}
                alt="Изображение к вопросу"
                className="max-h-64 object-contain"
              />
            </div>
          )}
        </CardHeader>
        <CardContent>{renderQuestionForm()}</CardContent>

        <CardFooter className="flex justify-end pt-6 border-t">
          <Button onClick={handleNext} disabled={isSubmitting}>
            {isLastQuestion ? "Завершить тест" : "Ответить"}
          </Button>
        </CardFooter>
      </Card>
    </div>
  );
}
