import { fetchPublicCategoryTreeAction } from "@/actions/category";
import { CategoryDTO } from "@/interfaces/category.interface";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import Link from "next/link";
import { ArrowRight, Factory, FolderOpen, ShieldCheck, Zap } from "lucide-react";

const getCategoryIcon = (slug: string) => {
  if (slug.includes("promyslennaa")) return <Factory className="h-7 w-7 text-orange-500" />;
  if (slug.includes("energeticeskaa")) return <Zap className="h-7 w-7 text-yellow-500" />;
  if (slug.includes("ohrana-truda")) return <ShieldCheck className="h-7 w-7 text-blue-500" />;
  return <FolderOpen className="h-7 w-7 text-primary" />;
};

export default async function HomePage() {
  const result = await fetchPublicCategoryTreeAction();

  if (!result.ok || !result.data) {
    return (
      <div className="flex h-full w-full items-center justify-center rounded-lg border border-dashed p-8 text-center text-muted-foreground">
        Сервис временно недоступен.
      </div>
    );
  }

  const tree: CategoryDTO[] = result.data;

  return (
    <div className="space-y-12 pb-10">
      {/* Hero секция */}
      <section className="space-y-4">
        <h1 className="text-4xl font-extrabold tracking-tight md:text-5xl">
          Платформа подготовки к аттестации в Ростехнадзоре
        </h1>
        <p className="max-w-[700px] text-lg text-muted-foreground">
          Подготовьтесь к аттестации в РТН по актуальным тестам. Выберите направление для подготовки
          и прохождения квалификационных тестов. Актуальная база вопросов и мгновенный результат.
        </p>
      </section>

      {/* Сетка главных категорий (2 колонки для ~1010px ширины) */}
      <section className="grid grid-cols-1 gap-6 sm:grid-cols-2">
        {tree.map((category) => (
          <Card
            key={category.id}
            className="relative flex flex-col transition-colors duration-200 hover:border-primary/50"
          >
            <CardHeader className="pb-4">
              <CardTitle className="flex items-center gap-3 text-xl">
                <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-muted/50 text-primary">
                  {getCategoryIcon(category.slug)}
                </div>
                <Link
                  href={`/catalog/${category.slug}`}
                  className="before:absolute before:inset-0 hover:text-primary focus:outline-none"
                >
                  {category.name}
                </Link>
              </CardTitle>
            </CardHeader>

            <CardContent className="relative z-10 flex flex-1 flex-col gap-2">
              {category.children && category.children.length > 0 ? (
                <div className="flex flex-col gap-1">
                  {category.children.slice(0, 3).map((sub) => (
                    <Link
                      key={sub.id}
                      href={`/catalog/${category.slug}/${sub.slug}`}
                      className="group flex items-center justify-between rounded-md p-2 -mx-2 text-sm transition-colors hover:bg-muted"
                    >
                      <span className="font-medium text-muted-foreground transition-colors group-hover:text-foreground">
                        {sub.name}
                      </span>
                      <ArrowRight className="h-4 w-4 text-primary opacity-0 transition-all group-hover:translate-x-1 group-hover:opacity-100" />
                    </Link>
                  ))}
                  {category.children.length > 3 && (
                    <Link
                      href={`/catalog/${category.slug}`}
                      className="mt-2 text-sm font-medium text-primary hover:underline"
                    >
                      Показать все направления ({category.children.length})
                    </Link>
                  )}
                </div>
              ) : (
                <Link
                  href={`/catalog/${category.slug}`}
                  className="group mt-auto inline-flex w-fit items-center text-sm font-medium text-muted-foreground transition-colors hover:text-primary"
                >
                  Перейти к тестам
                  <ArrowRight className="ml-1 h-4 w-4 transition-transform group-hover:translate-x-1" />
                </Link>
              )}
            </CardContent>
          </Card>
        ))}
      </section>
    </div>
  );
}
