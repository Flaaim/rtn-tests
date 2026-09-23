import { CategoryFull } from "@/interfaces/category.interface";
import { fetchCategoryAction, fetchCategoryTreeAction } from "@/actions/category";
import AdminBreadcrumbs from "@/components/Admin/AdminBreadcrumbs";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import RenameCategoryDialog from "@/components/Admin/Category/RenameCategoryDialog";
import MoveCategoryDialog from "@/components/Admin/Category/MoveCategoryDialog";
import { Badge } from "@/components/ui/badge";
import Link from "next/link";
import RemoveCategoryDialog from "@/components/Admin/Category/RemoveCategoryDialog";
import ChangeDescriptionCategoryDialog from "@/components/Admin/Category/ChangeDescriptionCategoryDialog";

interface CategoryOverviewPageProps {
  params: Promise<{ categoryId: string }>;
}
export default async function CourseOverviewPage({ params }: CategoryOverviewPageProps) {
  const { categoryId } = await params;

  const result = await fetchCategoryAction(categoryId);

  if (!result.ok || !result.data) {
    return null;
  }

  const category: CategoryFull = result.data;
  const items = [{ title: "Категории", href: "/admin/categories" }, { title: category.name }];

  const categoriesResult = await fetchCategoryTreeAction();
  const categoriesTree = categoriesResult.ok && categoriesResult.data ? categoriesResult.data : [];
  const hasChildren = category.children && category.children.length > 0;

  return (
    <div className="space-y-6">
      <AdminBreadcrumbs items={items} />
      <div className="flex items-center justify-between">
        <h1 className="text-3xl font-bold">Основная информация</h1>
      </div>
      <div className="space-y-6">
        <Card>
          <CardHeader>
            <div className="grid grid-cols-2 gap-4 items-center">
              <CardTitle>Категория: {category.name}</CardTitle>
              <div className="justify-self-end">
                <RenameCategoryDialog id={category.id} name={category.name} />
              </div>
            </div>
          </CardHeader>
          <CardContent className="space-y-4 text-sm">
            <p className="text-muted-foreground border-b pb-4">{category.description}</p>
            <div className="flex-shrink-0">
              <ChangeDescriptionCategoryDialog
                id={category.id}
                description={category.description}
              />
            </div>
            <div className="grid grid-cols-2 gap-4 sm:grid-cols-4 items-center">
              <div>
                <p className="text-muted-foreground font-medium">ID</p>
                <p className="font-mono">{category.id}</p>
                <MoveCategoryDialog categories={categoriesTree} category={category} />
              </div>

              <div className="hidden sm:block"></div>
              <div className="hidden sm:block"></div>

              <div className="justify-self-end text-right">
                <RemoveCategoryDialog id={category.id} />
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
      <div className="space-y-6">
        <h2 className="text-2xl font-bold tracking-tight">Дочерние категории:</h2>
        {hasChildren && <Badge variant="secondary">Найдено: {category.children.length}</Badge>}
      </div>
      {!hasChildren ? (
        <Card>
          <CardContent className="p-8 text-center text-muted-foreground">
            Дочерние категории отсутствуют...
          </CardContent>
        </Card>
      ) : (
        <Card>
          <CardContent>
            <div className="space-y-4">
              {category.children.map((cat: CategoryFull) => (
                <p key={cat.id} className="text-muted-foreground font-medium">
                  <Link href={`/admin/categories/${cat.id}`}> {cat.name}</Link>
                </p>
              ))}
            </div>
          </CardContent>
        </Card>
      )}
      <div className="space-y-6">
        <h2 className="text-2xl font-bold tracking-tight">Тесты:</h2>
      </div>
    </div>
  );
}
