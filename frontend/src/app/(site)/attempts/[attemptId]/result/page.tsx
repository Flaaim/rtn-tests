import { fetchAttemptResultAction } from "@/actions/attempt";
import { AttemptResultData, CategoryInfoDTO, TestInfoDTO } from "@/interfaces/attempt.interface";
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from "@/components/ui/card";
import { ResultQuestionCard } from "@/components/Testing/Attempt/ResultQuestionCard";
import { format } from "date-fns";
import { ru } from "date-fns/locale";
import { PrintButton } from "@/components/Testing/PrintButton";
import PublicTicketButton from "@/components/Testing/Test/PublicTicketButton";
import { checkIsAuthenticated } from "@/actions/auth";
import { BookOpen } from "lucide-react";
import Link from "next/link";

interface ResultPageProps {
  params: Promise<{ attemptId: string }>;
}

export default async function AttemptResultPage({ params }: ResultPageProps) {
  const { attemptId } = await params;
  const result = await fetchAttemptResultAction(attemptId);

  if (!result.ok || !result.data) {
    return <div className="p-8 text-center text-muted-foreground">Результаты не найдены.</div>;
  }

  const attemptData: AttemptResultData = result.data;
  const isPassed = attemptData.status === "passed";
  const test: TestInfoDTO = attemptData.test;
  const category: CategoryInfoDTO = attemptData.category;

  const testUrl = category.parent
    ? `/catalog/${category.parent.slug}/${category.slug}/${test.slug}`
    : `/catalog/${category.slug}/${test.slug}`;

  const isAuthenticated = await checkIsAuthenticated();

  return (
    <div className="max-w-4xl mx-auto space-y-8 py-8">
      <Card className={isPassed ? "border-green-500/50" : "border-red-500/50"}>
        <CardHeader className="text-center pb-2">
          <CardTitle className="text-3xl">
            {isPassed ? "Тест успешно сдан!" : "Тест не пройден"}
          </CardTitle>
          <p className="text-muted-foreground mt-2">
            {attemptData.test.name} ({attemptData.test.cipher})
          </p>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6 text-center">
            <div className="flex flex-col p-4 bg-muted/30 rounded-lg">
              <span className="text-sm text-muted-foreground">Пользователь</span>
              <span className="font-semibold truncate" title={attemptData.email}>
                {attemptData.email || "Гость"}
              </span>
            </div>
            <div className="flex flex-col p-4 bg-muted/30 rounded-lg">
              <span className="text-sm text-muted-foreground">Билет</span>
              <span className="font-semibold">{attemptData.ticketNumber}</span>
            </div>
            <div className="flex flex-col p-4 bg-green-500/10 rounded-lg">
              <span className="text-sm text-muted-foreground">Правильно</span>
              <span className="font-semibold text-green-700">{attemptData.score}</span>
            </div>
            <div className="flex flex-col p-4 bg-red-500/10 rounded-lg">
              <span className="text-sm text-muted-foreground">Ошибок</span>
              <span className="font-semibold text-red-700">
                {attemptData.mistakes} / {attemptData.test.allowedMistakes}
              </span>
            </div>
          </div>
          <div className="text-center mt-6 text-sm text-muted-foreground">
            Завершен:{" "}
            {format(
              new Date(attemptData.finishedAt || attemptData.startedAt),
              "dd MMMM yyyy, HH:mm",
              { locale: ru }
            )}
          </div>
        </CardContent>
        <CardFooter className="flex justify-center w-full">
          <div className="flex flex-row flex-wrap items-center justify-center w-full max-w-2xl">
            <div className="flex items-center gap-6">
              <PublicTicketButton
                testId={test.id}
                ticketNumber={attemptData.ticketNumber}
                isAuthenticated={isAuthenticated}
                label="Пройти еще раз"
              />
              <PrintButton />
              <Link className="inline-flex items-center gap-2" href={testUrl}>
                <BookOpen />
                Все вопросы теста
              </Link>
            </div>
          </div>
        </CardFooter>
      </Card>

      <div className="space-y-6">
        <h2 className="text-2xl font-bold">Разбор ответов</h2>
        {attemptData.questions.map((q, index) => (
          <ResultQuestionCard key={q.id} question={q} index={index} />
        ))}
      </div>
    </div>
  );
}
