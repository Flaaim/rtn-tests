import { Metadata } from "next";

import { checkIsAuthenticated } from "@/actions/auth";
import { notFound } from "next/navigation";
import Link from "next/link";
import { BookOpen, CheckCircle2, ChevronRight, Circle, Sparkles } from "lucide-react";
import { fetchPublicQuestionAction } from "@/actions/question";
import { Card, CardContent, CardHeader } from "@/components/ui/card";
import { PublicAnswerData } from "@/interfaces/question.interface";
import PublicTicketButton from "@/components/Testing/Test/PublicTicketButton";
import { Button } from "@base-ui/react";

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
        <ChevronRight className="h-4 w-4 shrink-0" />
        <Link
          href={`/categories/${question.category.slug}`}
          className="hover:text-foreground transition-colors"
        >
          {question.category.name}
        </Link>
        <ChevronRight className="h-4 w-4 shrink-0" />
        <Link
          href={`/tests/${question.test.slug}`}
          className="hover:text-foreground transition-colors font-medium text-foreground"
        >
          {question.test.name}
        </Link>
      </nav>

      <Card className="mb-8 border-border/80 shadow-sm">
        <CardHeader className="space-y-3 pb-4">
          <div className="flex items-center justify-between gap-2">
              Актуальная база вопросов
          </div>
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

      <div className="rounded-xl border bg-gradient-to-br from-primary/5 via-background to-primary/10 p-6 md:p-8 flex flex-col md:flex-row items-center justify-between gap-6">
        <div className="space-y-2 text-center md:text-left">
          <div className="inline-flex items-center gap-2 text-sm font-semibold text-primary">
            <Sparkles className="h-4 w-4" />
            <span>Бесплатный демо-режим без регистрации</span>
          </div>
          <h2 className="text-xl font-bold">
            Проверьте свои знания по курсу «{question.test.name}»
          </h2>
          <p className="text-sm text-muted-foreground max-w-xl">
            Не нужно зубрить ответы по одному. Запустите полноценный симулятор экзамена прямо сейчас
            (доступно 2 бесплатных билета) или перейдите ко всем билетам курса.
          </p>
        </div>

        <div className="flex flex-col sm:flex-row items-center gap-3 shrink-0">
          {/* Если добавил test.id в ответ бэкенда — запускаем сразу 1-й билет */}
          {question.test.id && (
            <div className="flex items-center gap-2">
              <PublicTicketButton
                testId={question.test.id}
                ticketNumber={1}
                isAuthenticated={isAuthenticated}
              />
              <span className="text-sm font-medium">Начать Билет №1</span>
            </div>
          )}

          <Button >
            <Link href={`/tests/${question.test.slug}`}>
              <BookOpen className="h-4 w-4 mr-2" />
              Все билеты курса
            </Link>
          </Button>
        </div>
      </div>
    </main>
  );
}
