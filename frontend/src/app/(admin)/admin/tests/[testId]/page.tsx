import AdminBreadcrumbs from "@/components/Admin/AdminBreadcrumbs";
import { TestFull } from "@/interfaces/test.interface";
import { fetchTestAction } from "@/actions/test";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import TestStatusBadge from "@/components/Admin/Test/Status/TestStatusBadge";
import { ChangeCipherTestDialog } from "@/components/Admin/Test/ChangeCipherTestDialog";
import RenameTestDialog from "@/components/Admin/Test/RenameTestDialog";

interface TestOverviewPageProps {
  params: Promise<{ testId: string }>;
}

export default async function TestOverviewPage({ params }: TestOverviewPageProps) {
  const { testId } = await params;

  const result = await fetchTestAction(testId);

  if (!result.ok || !result.data) {
    return null;
  }

  const test: TestFull = result.data;

  const items = [{ title: "Тесты", href: "/admin/tests" }, { title: test.name }];

  const formattedDate = new Date(test.createdAt).toLocaleString("ru-RU", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
  });

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
              <CardTitle>Курс: {test.name}</CardTitle>
              <div className="justify-self-end">
                <RenameTestDialog id={test.id} name={test.name} description={test.description} />
              </div>
            </div>
          </CardHeader>
          <CardContent className="space-y-4 text-sm">
            <div className="grid grid-cols-2 gap-4 sm:grid-cols-4">
              <div>
                <p className="text-muted-foreground font-medium">ID</p>
                <p className="font-mono">{test.id}</p>
              </div>
              <div>
                <p className="text-muted-foreground font-medium">Шифр</p>
                <p className="font-mono">
                  {test.cipher} <ChangeCipherTestDialog id={test.id} cipher={test.cipher} />
                </p>
              </div>
              <div>
                <p className="text-muted-foreground font-medium">Дата создания</p>
                <p className="font-mono">{formattedDate}</p>
              </div>
              <div>
                <p className="text-muted-foreground font-medium">Статус</p>
                <p className="font-mono">
                  <TestStatusBadge status={test.status} />
                </p>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
