import { fetchPublicCategoryTreeAction } from "@/actions/category";
import { notFound } from "next/navigation";
import Link from "next/link";
import { Card, CardHeader, CardTitle, CardContent } from "@/components/ui/card";
import { ChevronRight, Folder, ArrowRight } from "lucide-react";

interface CategoryPageProps {
  params: Promise<{ categorySlug: string }>;
}

export async function generateMetadata({ params }: CategoryPageProps) {
  const { categorySlug } = await params;

  try {
    const result = await fetchPublicCategoryTreeAction();

    if (!result.ok || !result.data) {
      return {
        title: "Сервис недоступен",
        description: "Не удалось загрузить данные категории.",
      };
    }

    const category = result.data.find((c) => c.slug === categorySlug);
    if (!category) {
      return {
        title: "Категория не найдена",
        description: "Запрашиваемая категория не существует.",
      };
    }

    return {
      title: category.name,
      description: category.description || `Перечень ${category.name}`,
    };
  } catch (error) {
    console.error(`Ошибка загрузки метаданных  ${categorySlug}:`, error);
    return {
      title: "Категория не найдена.",
      description: "Запрашиваемая категория не существует.",
    };
  }
}

export default async function CategoryPage({ params }: CategoryPageProps) {
  const { categorySlug } = await params;
  const result = await fetchPublicCategoryTreeAction();

  if (!result.ok || !result.data) {
    return (
      <div className="flex h-64 w-full items-center justify-center rounded-lg border border-dashed text-muted-foreground">
        Сервис временно недоступен.
      </div>
    );
  }

  // Ищем нужную родительскую категорию в дереве
  const category = result.data.find((c) => c.slug === categorySlug);

  if (!category) {
    notFound();
  }

  const subcategories = category.children || [];

  return (
    <div className="space-y-8 pb-10">
      {/* Хлебные крошки */}
      <nav className="flex items-center text-sm font-medium text-muted-foreground">
        <Link href="/" className="hover:text-foreground transition-colors">
          Главная
        </Link>
        <ChevronRight className="mx-2 h-4 w-4 shrink-0" />
        <Link href="/catalog" className="hover:text-foreground transition-colors">
          Каталог
        </Link>
        <ChevronRight className="mx-2 h-4 w-4 shrink-0" />
        <span className="text-foreground line-clamp-1">{category.name}</span>
      </nav>

      <div className="space-y-4">
        <h1 className="text-3xl font-extrabold tracking-tight md:text-4xl">{category.name}</h1>
        {category.description && (
          <p className="text-lg text-muted-foreground max-w-3xl">{category.description}</p>
        )}
      </div>

      {subcategories.length === 0 ? (
        <div className="rounded-lg border border-dashed p-8 text-center text-muted-foreground">
          В этой категории пока нет доступных направлений.
        </div>
      ) : (
        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
          {subcategories.map((sub) => (
            <Card
              key={sub.id}
              // Добавлен класс group для работы group-hover внутри
              className="group relative flex flex-col transition-colors duration-200 hover:border-primary/50 hover:shadow-sm"
            >
              <CardHeader className="pb-4">
                {/* items-start вместо items-center для корректного выравнивания многострочного текста */}
                <CardTitle className="flex items-start gap-3 text-lg leading-tight">
                  {/* Добавлен shrink-0 чтобы иконка не сжималась, и mt-1 чтобы выровнять с первой строкой текста */}
                  <Folder className="mt-0.5 h-5 w-5 shrink-0 text-muted-foreground transition-colors group-hover:text-primary" />
                  <Link
                    href={`/catalog/${category.slug}/${sub.slug}`}
                    className="before:absolute before:inset-0 hover:text-primary focus:outline-none"
                  >
                    <span className="line-clamp-2">{sub.name}</span>
                  </Link>
                </CardTitle>

                {/* Выводим описание категории, если оно есть */}
                {sub.description && (
                  <p className="mt-2 text-sm text-muted-foreground line-clamp-2">
                    {sub.description}
                  </p>
                )}
              </CardHeader>

              <CardContent className="mt-auto pt-4 flex items-center justify-between text-sm font-medium text-muted-foreground transition-colors group-hover:text-primary">
                Перейти к тестам
                {/* Добавлена небольшая анимация движения стрелки */}
                <ArrowRight className="h-4 w-4 transition-transform duration-200 group-hover:translate-x-1" />
              </CardContent>
            </Card>
          ))}
        </div>
      )}
    </div>
  );
}
