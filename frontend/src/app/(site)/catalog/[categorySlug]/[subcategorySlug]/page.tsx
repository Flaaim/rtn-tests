import { fetchPublicCategoryTreeAction } from "@/actions/category";
import { fetchPublicTestsByCategoryAction } from "@/actions/test";
import { notFound } from "next/navigation";
import Link from "next/link";
import { Card, CardHeader, CardTitle, CardContent } from "@/components/ui/card";
import { ChevronRight, FileText, ArrowRight, Calendar } from "lucide-react";
import { TestItemPublic } from "@/interfaces/test.interface";

interface SubcategoryPageProps {
  params: Promise<{ categorySlug: string; subcategorySlug: string }>;
}

export async function generateMetadata({ params }: SubcategoryPageProps) {
  const { categorySlug, subcategorySlug } = await params;

  try {
    const result = await fetchPublicCategoryTreeAction();
    if (!result.ok || !result.data) {
      return {
        title: "Сервис недоступен",
        description: "Не удалось загрузить данные подкатегории.",
      };
    }
    const parentCategory = result.data?.find((c) => c.slug === categorySlug);
    const subcategory = parentCategory?.children?.find((c) => c.slug === subcategorySlug);
    if (!parentCategory || !subcategory) {
      return {
        title: "Подкатегория не найдена",
        description: "Запрашиваемая подкатегория не существует.",
      };
    }
    return {
      title: subcategory.name,
      description: subcategory.description || `Перечень ${subcategory.name}`,
    };
  } catch (error) {
    console.error(`Ошибка загрузки метаданных  ${subcategorySlug}:`, error);
    return {
      title: "Подкатегория не найдена.",
      description: "Запрашиваемая подкатегория не существует.",
    };
  }
}

export default async function SubcategoryPage({ params }: SubcategoryPageProps) {
  const { categorySlug, subcategorySlug } = await params;

  // 1. Получаем дерево, чтобы построить крошки и найти название текущей подкатегории
  const treeResult = await fetchPublicCategoryTreeAction();
  if (!treeResult.ok || !treeResult.data) {
    notFound();
  }

  const parentCategory = treeResult.data?.find((c) => c.slug === categorySlug);
  const subcategory = parentCategory?.children?.find((c) => c.slug === subcategorySlug);

  if (!parentCategory || !subcategory) {
    notFound();
  }

  // 2. Получаем список активных тестов
  const testsResult = await fetchPublicTestsByCategoryAction(subcategorySlug);

  if (!testsResult.ok || !testsResult.data) {
    return (
      <div className="flex h-64 w-full items-center justify-center rounded-lg border border-dashed text-muted-foreground">
        Не удалось загрузить доступные тесты. Возможно они еще не добавлены...
      </div>
    );
  }
  const tests = testsResult.data || [];

  return (
    <div className="space-y-8 pb-10">
      {/* Хлебные крошки */}
      <nav className="flex flex-wrap items-center text-sm font-medium text-muted-foreground">
        <Link href="/" className="hover:text-foreground transition-colors">
          Главная
        </Link>
        <ChevronRight className="mx-2 h-4 w-4 shrink-0" />
        <Link href="/catalog" className="hover:text-foreground transition-colors">
          Каталог
        </Link>
        <ChevronRight className="mx-2 h-4 w-4 shrink-0" />
        <Link
          href={`/catalog/${categorySlug}`}
          className="hover:text-foreground transition-colors line-clamp-1 max-w-[150px] sm:max-w-none"
        >
          {parentCategory?.name}
        </Link>
        <ChevronRight className="mx-2 h-4 w-4 shrink-0" />
        <span className="text-foreground line-clamp-1">{subcategory.name}</span>
      </nav>

      {/* Заголовок и описание */}
      <div className="space-y-4">
        <h1 className="text-3xl font-extrabold tracking-tight md:text-4xl">{subcategory.name}</h1>
        {subcategory.description && (
          <p className="text-lg text-muted-foreground max-w-3xl">{subcategory.description}</p>
        )}
      </div>

      {tests.length === 0 ? (
        <div className="rounded-lg border border-dashed p-8 text-center text-muted-foreground">
          В этом разделе пока нет активных тестов.
        </div>
      ) : (
        <div className="grid grid-cols-1 gap-4">
          {tests.map((test: TestItemPublic) => {
            const formattedDate = new Date(test.createdAt).toLocaleDateString("ru-RU", {
              day: "2-digit",
              month: "2-digit",
              year: "numeric",
            });

            return (
              <Card
                key={test.id}
                className="group relative flex flex-col transition-colors duration-200 hover:border-primary/50 hover:shadow-sm"
              >
                <CardHeader className="pb-3">
                  <div className="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                    <CardTitle className="flex items-start gap-3 text-xl leading-tight">
                      <FileText className="mt-0.5 h-5 w-5 shrink-0 text-muted-foreground transition-colors group-hover:text-primary" />
                      <Link
                        href={`/catalog/${categorySlug}/${subcategorySlug}/${test.slug}`}
                        className="before:absolute before:inset-0 hover:text-primary focus:outline-none flex flex-col gap-1 sm:block"
                      >
                        {/* Выделяем шифр, чтобы он не сливался с текстом */}
                        {test.cipher && (
                          <span className="text-primary font-bold mr-2">{test.cipher}</span>
                        )}
                        <span className="group-hover:text-primary transition-colors">
                          {test.name}
                        </span>
                      </Link>
                    </CardTitle>

                    {/* Дата создания - безопасно позиционирована справа на десктопе и сверху на мобилках */}
                    <div className="flex items-center gap-1.5 shrink-0 text-xs text-muted-foreground sm:pt-1">
                      <Calendar className="h-3.5 w-3.5" />
                      <span>{formattedDate}</span>
                    </div>
                  </div>
                </CardHeader>

                <CardContent className="mt-auto flex flex-col gap-4 relative z-10">
                  {test.description && (
                    <p className="text-muted-foreground line-clamp-2 text-sm md:text-base">
                      {test.description}
                    </p>
                  )}

                  <div className="flex items-center text-sm font-medium text-muted-foreground transition-colors group-hover:text-primary pt-2">
                    Перейти к билетам
                    <ArrowRight className="ml-1.5 h-4 w-4 transition-transform duration-200 group-hover:translate-x-1" />
                  </div>
                </CardContent>
              </Card>
            );
          })}
        </div>
      )}
    </div>
  );
}
