import {
  Calendar,
  ChevronRight,
  FileQuestion,
  ListChecks,
  ShieldAlert,
  Ticket,
} from "lucide-react";
import { Card, CardHeader, CardTitle, CardContent } from "@/components/ui/card";
import Link from "next/link";
import { fetchPublicCategoryTreeAction } from "@/actions/category";
import { notFound } from "next/navigation";
import { fetchPublicTestBySlugAction } from "@/actions/test";
import { TestPublicDTO } from "@/interfaces/test.interface";
import PublicTicketButton from "@/components/Testing/Test/PublicTicketButton";
import { checkIsAuthenticated } from "@/actions/auth";

interface TestSlugPageProps {
  params: Promise<{ categorySlug: string; subcategorySlug: string; testSlug: string }>;
}

export async function generateMetadata({ params }: TestSlugPageProps) {
  const { testSlug } = await params;

  try {
    const result = await fetchPublicTestBySlugAction(testSlug);
    if (!result.ok || !result.data) {
      return {
        title: "Сервис недоступен",
        description: "Не удалось загрузить данные теста.",
      };
    }
    const test: TestPublicDTO = result.data;
    return {
      title: `Тест: ${test.cipher} ${test.name.toLowerCase()}`,
      description: test.description || `Тест для проверки знаний ${test.name}`,
    };
  } catch (error) {
    console.error(`Ошибка загрузки метаданных  ${testSlug}:`, error);
    return {
      title: "Тест не найден.",
      description: "Запрашиваемый тест не существует.",
    };
  }
}

export default async function TestSlugPage({ params }: TestSlugPageProps) {
  const { categorySlug, subcategorySlug, testSlug } = await params;

  const treeResult = await fetchPublicCategoryTreeAction();

  if (!treeResult.ok || !treeResult.data) {
    notFound();
  }

  const parentCategory = treeResult.data?.find((c) => c.slug === categorySlug);
  const subcategory = parentCategory?.children?.find((c) => c.slug === subcategorySlug);

  if (!parentCategory || !subcategory) {
    notFound();
  }

  const testResult = await fetchPublicTestBySlugAction(testSlug);

  if (!testResult.ok || !testResult.data) {
    notFound();
  }
  const test: TestPublicDTO = testResult.data;

  const isAuthenticated = await checkIsAuthenticated();

  const formattedDate = new Date(test.createdAt).toLocaleDateString("ru-RU", {
    year: "numeric",
    month: "long",
    day: "numeric",
  });

  return (
    <div className="space-y-8 pb-10">
      {/* Хлебные крошки */}
      <nav className="flex flex-wrap items-center text-sm font-medium text-muted-foreground">
        <Link href="/" className="hover:text-foreground transition-colors">
          Главная
        </Link>
        <ChevronRight className="mx-2 h-4 w-4" />
        <Link href="/catalog" className="hover:text-foreground transition-colors">
          Каталог
        </Link>
        <ChevronRight className="mx-2 h-4 w-4" />
        <Link href={`/catalog/${categorySlug}`} className="hover:text-foreground transition-colors">
          {parentCategory?.name}
        </Link>
        <ChevronRight className="mx-2 h-4 w-4" />
        <span className="text-foreground">{subcategory.name}</span>
      </nav>

      <section className="flex flex-col items-start gap-5">
        <div className="flex flex-wrap items-center gap-4">
          <div className="inline-flex items-center rounded-md border border-primary/20 bg-primary/10 px-3 py-1 text-sm font-semibold text-primary">
            {test.cipher}
          </div>
          <div className="flex items-center text-sm text-muted-foreground">
            <Calendar className="mr-2 h-4 w-4" />
            Добавлен: {formattedDate}
          </div>
        </div>
        <h1 className="text-3xl font-extrabold tracking-tight md:text-5xl">{test.name}</h1>
        <p className="max-w-3xl text-lg leading-relaxed text-muted-foreground">
          {test.description}
        </p>
      </section>

      <section className="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <Card className="bg-muted/40 shadow-none">
          <CardContent className="flex items-center gap-4 p-6">
            <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10 text-primary">
              <FileQuestion className="h-6 w-6" />
            </div>
            <div>
              <p className="text-sm font-medium text-muted-foreground">Вопросов в билете</p>
              <p className="text-2xl font-bold">{test.settings.numberQuestionsInTicket}</p>
            </div>
          </CardContent>
        </Card>

        <Card className="bg-muted/40 shadow-none">
          <CardContent className="flex items-center gap-4 p-6">
            <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-green-500/10 text-green-600">
              <ListChecks className="h-6 w-6" />
            </div>
            <div>
              <p className="text-sm font-medium text-muted-foreground">Всего билетов</p>
              <p className="text-2xl font-bold">{test.settings.numberOfTickets}</p>
            </div>
          </CardContent>
        </Card>

        <Card className="bg-muted/40 shadow-none">
          <CardContent className="flex items-center gap-4 p-6">
            <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-destructive/10 text-destructive">
              <ShieldAlert className="h-6 w-6" />
            </div>
            <div>
              <p className="text-sm font-medium text-muted-foreground">Допустимых ошибок</p>
              <p className="text-2xl font-bold">{test.settings.allowedMistakes}</p>
            </div>
          </CardContent>
        </Card>
      </section>

      <section className="space-y-6 pt-4">
        <h2 className="text-2xl font-bold tracking-tight">Билеты для подготовки</h2>
        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
          {test.tickets.map((ticket) => (
            <Card
              key={ticket.number}
              className="group relative flex flex-col transition-all duration-300 hover:border-primary/50 hover:shadow-md"
            >
              <CardHeader className="pb-4">
                <CardTitle className="flex items-center gap-3 text-xl transition-colors group-hover:text-primary">
                  <div className="flex h-10 w-10 items-center justify-center rounded-full bg-muted transition-colors group-hover:bg-primary/10">
                    <Ticket className="h-5 w-5" />
                  </div>
                  Билет {ticket.number}
                </CardTitle>
              </CardHeader>
              <CardContent className="mt-auto flex items-center justify-between pb-4">
                <span className="text-sm font-medium text-muted-foreground text-center">
                  {test.settings.numberQuestionsInTicket} вопросов
                </span>
                <div className="flex h-8 w-8 items-center justify-center rounded-full text-muted-foreground transition-colors group-hover:bg-primary group-hover:text-primary-foreground">
                  <PublicTicketButton
                    testId={test.id}
                    ticketNumber={ticket.number}
                    isAuthenticated={isAuthenticated}
                  />
                </div>
              </CardContent>
            </Card>
          ))}
        </div>
      </section>
    </div>
  );
}
