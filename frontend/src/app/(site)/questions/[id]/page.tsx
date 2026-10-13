import { Metadata } from "next";

import { checkIsAuthenticated } from "@/actions/auth";
import { notFound } from "next/navigation";
import Link from "next/link";
import { CheckCircle2, ChevronRight, Circle, Sparkles } from "lucide-react";
import { fetchPublicQuestionAction } from "@/actions/question";
import { Card, CardContent, CardHeader } from "@/components/ui/card";
import { PublicAnswerData } from "@/interfaces/question.interface";
import PublicTicketButton from "@/components/Testing/Test/PublicTicketButton";
import { Button } from "@/components/ui/button";

interface PageProps {
  params: Promise<{ id: string }>;
}

export async function generateMetadata({ params }: PageProps): Promise<Metadata> {
  const { id } = await params;
  const result = await fetchPublicQuestionAction(id);

  if (!result.ok || !result.data) {
    return { title: "Вопрос не найден" };
  }

  const q = result.data;
  const cleanTitle = q.text.length > 65 ? `${q.text.slice(0, 65)}...` : q.text;

  return {
    title: `${cleanTitle} — Ответ по курсу «${q.test.name}»`,
    description: `Правильный ответ на вопрос теста Ростехнадзора: «${q.text.slice(0, 140)}». Разбор и бесплатное тестирование без регистрации.`,
    alternates: {
      canonical: `/questions/${q.id}`,
    },
  };
}

export default async function PublicQuestionPage({ params }: PageProps) {
  const { id } = await params;
  const [result, isAuthenticated] = await Promise.all([
    fetchPublicQuestionAction(id),
    checkIsAuthenticated(),
  ]);

  if (!result.ok || !result.data) {
    notFound();
  }

  const question = result.data;

  const correctAnswersText = question.answers
    .filter((answer) => answer.isCorrect)
    .map((a) => a.text)
    .join("; ");

  const testUrl = question.category.parent
    ? `/catalog/${question.category.parent.slug}/${question.category.slug}/${question.test.slug}`
    : `/catalog/${question.category.slug}/${question.test.slug}`;

  const jsonLd = {
    "@context": "https://schema.org",
    "@type": "QAPage",
    mainEntity: {
      "@type": "Question",
      name: question.text,
      text: question.text,
      answerCount: 1,
      acceptedAnswer: {
        "@type": "Answer",
        text: correctAnswersText || "Правильный ответ выделен в списке.",
      },
    },
  };

  return (
    <main className="container max-w-4xl mx-auto py-8 px-4">
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(jsonLd) }}
      />
      <nav className="flex flex-wrap items-center gap-1.5 text-sm text-muted-foreground mb-6">
        <Link href="/" className="hover:text-foreground transition-colors">
          Главная
        </Link>

        {question.category.parent && (
          <>
            <ChevronRight className="h-4 w-4 shrink-0" />
            <Link
              href={`/catalog/${question.category.parent.slug}`}
              className="hover:text-foreground transition-colors"
            >
              {question.category.parent.name}
            </Link>
          </>
        )}

        <ChevronRight className="h-4 w-4 shrink-0" />
        <Link
          href={
            question.category.parent
              ? `/catalog/${question.category.parent.slug}/${question.category.slug}`
              : `/catalog/${question.category.slug}`
          }
          className="hover:text-foreground transition-colors"
        >
          {question.category.name}
        </Link>

        <ChevronRight className="h-4 w-4 shrink-0" />
        <Link
          href={testUrl}
          className="hover:text-foreground transition-colors font-medium text-foreground"
        >
          {question.test.name}
        </Link>
      </nav>

      <Card className="mb-8 border-border/80 shadow-sm">
        <CardHeader className="space-y-3 pb-4">
          <div className="flex items-center justify-between gap-2">Актуальная база вопросов</div>
          <h1 className="text-xl md:text-2xl font-bold leading-snug">{question.text}</h1>
        </CardHeader>

        <CardContent className="space-y-3">
          <p className="text-xs font-medium uppercase tracking-wider text-muted-foreground mb-2">
            Варианты ответа:
          </p>

          {question.answers.map((answer: PublicAnswerData, index) => {
            const correct = answer.isCorrect;

            return (
              <div
                key={answer.id ?? index}
                className={`flex items-start gap-3 p-4 rounded-lg border transition-colors ${
                  correct
                    ? "bg-emerald-50/70 border-emerald-300 dark:bg-emerald-950/30 dark:border-emerald-800"
                    : "bg-card border-border/60 text-muted-foreground"
                }`}
              >
                {answer.isCorrect ? (
                  <CheckCircle2 className="h-5 w-5 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" />
                ) : (
                  <Circle className="h-5 w-5 text-muted-foreground/40 shrink-0 mt-0.5" />
                )}
                <div className="space-y-1">
                  <span
                    className={
                      correct
                        ? "font-medium text-emerald-950 dark:text-emerald-100"
                        : "text-foreground/80"
                    }
                  >
                    {answer.text}
                  </span>
                  {correct && (
                    <span className="block text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                      ✓ Правильный ответ
                    </span>
                  )}
                </div>
              </div>
            );
          })}
        </CardContent>
      </Card>

      <div className="relative overflow-hidden rounded-2xl border border-primary/20 bg-gradient-to-br from-primary/[0.07] via-background to-primary/[0.12] p-6 md:p-8 shadow-sm">
        <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
          <div className="space-y-2.5 text-center lg:text-left">
            <div className="inline-flex items-center gap-2 rounded-full bg-primary/10 px-3 py-1 text-xs font-semibold text-primary">
              <Sparkles className="h-3.5 w-3.5" />
              <span>Тренажер экзамена Ростехнадзора • Без регистрации</span>
            </div>

            <h2 className="text-xl md:text-2xl font-bold tracking-tight">
              Готовы проверить знания по курсу «{question.test.name}»?
            </h2>

            <p className="text-sm md:text-base text-muted-foreground max-w-2xl">
              Не нужно учить вопросы вразнобой. Запустите режим реального экзамена прямо сейчас
              (доступно 2 бесплатных билета) или откройте полный список билетов и вопросов курса.
            </p>
          </div>

          {/* Кнопки действий */}
          <div className="flex flex-col sm:flex-row lg:flex-col xl:flex-row items-stretch sm:items-center justify-center gap-3 shrink-0">
            {question.test.id && (
              <PublicTicketButton
                testId={question.test.id}
                ticketNumber={1}
                isAuthenticated={isAuthenticated}
                label="Начать экзамен (Билет №1)"
              />
            )}

            <Button
              variant="outline"
              size="lg"
              className="w-full sm:w-auto bg-background/80 hover:bg-background font-medium"
            >
              <Link href={testUrl}>Все вопросы курса</Link>
            </Button>
          </div>
        </div>
      </div>
    </main>
  );
}
