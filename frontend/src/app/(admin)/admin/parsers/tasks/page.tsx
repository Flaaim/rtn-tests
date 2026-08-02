import AdminBreadcrumbs from "@/components/Admin/AdminBreadcrumbs";
import { AlertCircle } from "lucide-react";
import { fetchTasksAction } from "@/actions/task";
import { TasksTable } from "@/components/Admin/Task/TasksTable";

export default async function AdminTasksPage() {
  const result = await fetchTasksAction();

  if (!result.ok || !result.data) {
    return (
      <div className="space-y-6">
        <AdminBreadcrumbs items={[{ title: "Результаты парсинга" }]} />
        <div className="flex items-center justify-between">
          <h1 className="text-3xl font-bold">Парсеры</h1>
        </div>
        <div className="mx-auto max-w-4xl p-4 md:p-8">
          <div className="flex min-h-[40vh] flex-col items-center justify-center space-y-4 text-center">
            <div className="flex size-16 items-center justify-center rounded-full bg-destructive/10 text-destructive">
              <AlertCircle className="size-8" />
            </div>
            <h2 className="text-xl font-semibold">Не удалось загрузить резульаты</h2>
            <p className="text-sm text-muted-foreground">
              {result.error ?? "Произошла непредвиденная ошибка"}
            </p>
          </div>
        </div>
      </div>
    );
  }
  return (
    <div className="space-y-6">
      <AdminBreadcrumbs items={[{ title: "Результаты парсинга" }]} />
      <div className="flex items-center justify-between">
        <h1 className="text-3xl font-bold">Результаты парсинга</h1>
      </div>
      <TasksTable tasks={result.data} />
    </div>
  );
}
