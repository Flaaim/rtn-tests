import AdminBreadcrumbs from "@/components/Admin/AdminBreadcrumbs";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import { AlertCircle } from "lucide-react";
import { fetchTasksAction } from "@/actions/task";
import { TaskShort } from "@/interfaces/task.interface";

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
      <div className="rounded-md border bg-white">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Номер задачи</TableHead>
              <TableHead>Номер парсера</TableHead>
              <TableHead>Создан</TableHead>
              <TableHead>Статус</TableHead>
              <TableHead>Ошибки</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {result.data.map((task: TaskShort) => (
              <TableRow key={task.id}>
                <TableCell className="font-medium">{task.id}</TableCell>
                <TableCell className="font-medium">{task.parserId}</TableCell>
                <TableCell className="font-medium">
                  {new Date(task.created).toLocaleDateString("ru-RU")}
                </TableCell>
                <TableCell className="font-medium">{task.status}</TableCell>
                <TableCell className="font-medium">{task.failedReason}</TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </div>
    </div>
  );
}
