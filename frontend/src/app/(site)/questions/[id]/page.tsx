import { Metadata } from "next";

import { checkIsAuthenticated } from "@/actions/auth";
import { notFound } from "next/navigation";
import Link from "next/link";
import { CheckCircle2, ChevronRight, FileText, Sparkles } from "lucide-react";
import { fetchPublicQuestionAction } from "@/actions/question";
import { Card, CardContent, CardHeader } from "@/components/ui/card";
import PublicTicketButton from "@/components/Testing/Test/PublicTicketButton";
import { Button } from "@/components/ui/button";
import ShowCorrectAnswer from "@/components/Question/ShowCorrectAnswer";
import { isOpenQuestion, ProductDTO, PublicTestData } from "@/interfaces/question.interface";
import PurchaseProduct from "@/components/Product/PurchaseProduct";

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
  const product: ProductDTO | null = result.data.product;

  const formattedPrice = product ? Math.round(Number(product.amount)) : 0;
  const isOpen = isOpenQuestion(question);

  const correctAnswersText = isOpen
    ? question.answers
        .filter((answer) => answer.isCorrect)
        .map((a) => a.text)
        .join("; ")
    : null;

  const test: PublicTestData = question.test;

  const testUrl = test.category.parent
    ? `/catalog/${test.category.parent.slug}/${test.category.slug}/${test.slug}`
    : `/catalog/${test.category.slug}/${test.slug}`;

  const jsonLd = {
    "@context": "https://schema.org",
    "@type": "QAPage",
    mainEntity: {
      "@type": "Question",
      name: question.text,
      text: question.text,
      answerCount: correctAnswersText ? 1 : 0,
      ...(correctAnswersText && {
        acceptedAnswer: {
          "@type": "Answer",
          text: correctAnswersText,
        },
      }),
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

        {test.category.parent && (
          <>
            <ChevronRight className="h-4 w-4 shrink-0" />
            <Link
              href={`/catalog/${test.category.parent.slug}`}
              className="hover:text-foreground transition-colors"
            >
              {test.category.parent.name}
            </Link>
          </>
        )}

        <ChevronRight className="h-4 w-4 shrink-0" />
        <Link
          href={
            test.category.parent
              ? `/catalog/${test.category.parent.slug}/${test.category.slug}`
              : `/catalog/${test.category.slug}`
          }
          className="hover:text-foreground transition-colors"
        >
          {test.category.name}
        </Link>

        <ChevronRight className="h-4 w-4 shrink-0" />
        <Link
          href={testUrl}
          className="hover:text-foreground transition-colors font-medium text-foreground"
        >
          {test.name}
        </Link>
      </nav>

      <Card className="mb-8 border-border/80 shadow-sm">
        <CardHeader className="space-y-3 pb-4">
          <div className="flex items-center justify-between gap-2">
            <span>Актуальная база вопросов</span>
            {!isOpen && (
              <span className="text-xs font-medium text-amber-600 bg-amber-500/10 px-2.5 py-1 rounded-full">
                Ответ в закрытом доступе. Правильные ответы доступны в файле с ответами.
              </span>
            )}
          </div>
          <h1 className="text-xl md:text-2xl font-bold leading-snug">{question.text}</h1>
        </CardHeader>

        <CardContent className="space-y-3">
          <p className="text-xs font-medium uppercase tracking-wider text-muted-foreground mb-2">
            Варианты ответа:
          </p>

          <ShowCorrectAnswer question={question} isAuthenticated={isAuthenticated} />
        </CardContent>
      </Card>

      <div className="space-y-6 mb-8">
        {product && (
          <Card className="overflow-hidden border-primary/20 bg-primary/5">
            <div className="flex flex-col md:flex-row">
              <div className="bg-primary/10 p-6 md:w-1/3 flex flex-col items-center justify-center border-b md:border-b-0 md:border-r border-primary/10">
                <div className="w-24 h-32 bg-background rounded shadow-md border flex flex-col p-3 relative">
                  <div className="w-full h-2 bg-primary/20 rounded mb-2" />
                  <div className="w-3/4 h-2 bg-primary/20 rounded mb-4" />
                  <div className="space-y-1.5 mt-auto">
                    <div className="w-full h-1.5 bg-muted rounded" />
                    <div className="w-full h-1.5 bg-muted rounded" />
                    <div className="w-4/5 h-1.5 bg-muted rounded" />
                  </div>
                  <div className="absolute -bottom-3 -right-3 bg-primary text-primary-foreground p-2 rounded-full shadow-lg">
                    <FileText className="w-5 h-5" />
                  </div>
                </div>
              </div>

              {/* Описание и покупка */}
              <div className="p-6 md:w-2/3 flex flex-col justify-center">
                <h3 className="text-lg font-bold mb-2">Полный сборник ответов: {product.name}</h3>
                <ul className="space-y-2 mb-6 text-sm text-muted-foreground">
                  <li className="flex items-start gap-2">
                    <CheckCircle2 className="w-4 h-4 text-green-500 mt-0.5 shrink-0" />
                    <span>Все актуальные вопросы со 100% правильными ответами</span>
                  </li>
                  <li className="flex items-start gap-2">
                    <CheckCircle2 className="w-4 h-4 text-green-500 mt-0.5 shrink-0" />
                    <span>Удобный PDF-формат для печати и чтения с телефона (шпаргалка)</span>
                  </li>
                  <li className="flex items-start gap-2">
                    <CheckCircle2 className="w-4 h-4 text-green-500 mt-0.5 shrink-0" />
                    <span>Моментальный доступ к файлу после оплаты</span>
                  </li>
                </ul>

                <div className="flex flex-col sm:flex-row items-center gap-4 mt-auto">
                  <PurchaseProduct
                    id={product.productId}
                    name={product.name}
                    amount={product.amount}
                    variant="dialog"
                  />
                  {/* Кнопка превью, если у вас есть демо-версия файла */}
                  {/*<Button variant="outline" className="w-full sm:w-auto" asChild>*/}
                  {/*  <a href={pdfUrl} target="_blank" rel="noopener noreferrer">*/}
                  {/*    Посмотреть фрагмент*/}
                  {/*  </a>*/}
                  {/*</Button>*/}
                </div>
              </div>
            </div>
          </Card>
        )}
      </div>

      <div className="space-y-6">
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
                или откройте полный список билетов и вопросов курса.
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
      </div>
    </main>
  );
}
